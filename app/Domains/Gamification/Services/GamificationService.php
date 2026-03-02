<?php

namespace App\Domains\Gamification\Services;

use App\Domains\Gamification\Data\GamificationSummaryData;
use App\Domains\Gamification\Models\Achievement;
use App\Domains\Gamification\Models\PatientAchievement;
use App\Domains\Gamification\Models\PatientPoint;
use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\ActivityEntry;
use App\Domains\Tracking\Models\WeightEntry;

class GamificationService
{
    public function getSummary(Patient $patient): GamificationSummaryData
    {
        $points = PatientPoint::firstOrCreate(
            ['patient_id' => $patient->id],
            ['total_points' => 0, 'current_streak_days' => 0, 'longest_streak_days' => 0]
        );

        $recentAchievements = PatientAchievement::where('patient_id', $patient->id)
            ->with('achievement')
            ->orderByDesc('unlocked_at')
            ->limit(5)
            ->get()
            ->map(fn ($pa) => [
                'id' => $pa->id,
                'code' => $pa->achievement->code,
                'name' => $pa->achievement->name,
                'description' => $pa->achievement->description,
                'icon' => $pa->achievement->icon,
                'points' => $pa->achievement->points,
                'category' => $pa->achievement->category,
                'unlocked_at' => $pa->unlocked_at->toISOString(),
            ]);

        $totalCount = Achievement::count();
        $unlockedCount = PatientAchievement::where('patient_id', $patient->id)->count();

        return GamificationSummaryData::fromModels(
            $points,
            $recentAchievements,
            $totalCount,
            $unlockedCount,
        );
    }

    public function getPatientAchievements(Patient $patient): array
    {
        $allAchievements = Achievement::orderBy('category')->orderBy('points')->get();

        $unlockedIds = PatientAchievement::where('patient_id', $patient->id)
            ->pluck('unlocked_at', 'achievement_id')
            ->toArray();

        return $allAchievements->map(function ($achievement) use ($unlockedIds) {
            $isUnlocked = array_key_exists($achievement->id, $unlockedIds);
            return [
                'id' => $achievement->id,
                'code' => $achievement->code,
                'name' => $achievement->name,
                'description' => $achievement->description,
                'icon' => $achievement->icon,
                'points' => $achievement->points,
                'category' => $achievement->category,
                'is_unlocked' => $isUnlocked,
                'unlocked_at' => $isUnlocked ? $unlockedIds[$achievement->id] : null,
            ];
        })->toArray();
    }

    public function getAchievementProximity(Patient $patient): array
    {
        $allAchievements = Achievement::orderBy('category')->orderBy('points')->get();

        $unlockedCodes = PatientAchievement::where('patient_id', $patient->id)
            ->with('achievement')
            ->get()
            ->pluck('achievement.code')
            ->toArray();

        // Gather current metrics
        $weightEntries = WeightEntry::where('patient_id', $patient->id)
            ->orderBy('recorded_at')
            ->get();

        $totalWeightLost = $weightEntries->count() >= 2
            ? (float) $weightEntries->first()->weight_kg - (float) $weightEntries->last()->weight_kg
            : 0;

        $currentBmi = $weightEntries->last()?->bmi ? (float) $weightEntries->last()->bmi : null;
        $weightEntryCount = $weightEntries->count();

        $totalActivities = ActivityEntry::whereHas('patientDay', fn ($q) => $q->where('patient_id', $patient->id))->count();

        $points = PatientPoint::where('patient_id', $patient->id)->first();
        $currentStreak = $points?->current_streak_days ?? 0;

        $thresholds = [
            'weight_5kg_lost' => ['metric' => 'weight_lost', 'target' => 5],
            'weight_10kg_lost' => ['metric' => 'weight_lost', 'target' => 10],
            'weight_20kg_lost' => ['metric' => 'weight_lost', 'target' => 20],
            'weight_30kg_lost' => ['metric' => 'weight_lost', 'target' => 30],
            'weight_50kg_lost' => ['metric' => 'weight_lost', 'target' => 50],
            'weight_10_entries' => ['metric' => 'weight_entries', 'target' => 10],
            'activity_10' => ['metric' => 'total_activities', 'target' => 10],
            'activity_25' => ['metric' => 'total_activities', 'target' => 25],
            'activity_50' => ['metric' => 'total_activities', 'target' => 50],
            'activity_100' => ['metric' => 'total_activities', 'target' => 100],
            'streak_3' => ['metric' => 'streak', 'target' => 3],
            'streak_7' => ['metric' => 'streak', 'target' => 7],
            'streak_14' => ['metric' => 'streak', 'target' => 14],
            'streak_30' => ['metric' => 'streak', 'target' => 30],
            'streak_60' => ['metric' => 'streak', 'target' => 60],
            'streak_90' => ['metric' => 'streak', 'target' => 90],
            'bmi_below_40' => ['metric' => 'bmi_below', 'target' => 40],
            'bmi_below_35' => ['metric' => 'bmi_below', 'target' => 35],
            'bmi_below_30' => ['metric' => 'bmi_below', 'target' => 30],
            'bmi_below_25' => ['metric' => 'bmi_below', 'target' => 25],
        ];

        $metricValues = [
            'weight_lost' => $totalWeightLost,
            'weight_entries' => $weightEntryCount,
            'total_activities' => $totalActivities,
            'streak' => $currentStreak,
        ];

        $nearAchievements = [];

        foreach ($allAchievements as $achievement) {
            $code = $achievement->code;

            if (in_array($code, $unlockedCodes) || ! isset($thresholds[$code])) {
                continue;
            }

            $threshold = $thresholds[$code];
            $target = $threshold['target'];

            if ($threshold['metric'] === 'bmi_below') {
                if ($currentBmi === null) {
                    continue;
                }

                $remaining = $currentBmi - $target;
                if ($remaining > 0 && $remaining <= 2) {
                    $nearAchievements[] = [
                        'code' => $code,
                        'name' => $achievement->name,
                        'current' => round($currentBmi, 1),
                        'target' => $target,
                        'remaining' => round($remaining, 1),
                        'unit' => 'points IMC',
                        'progress_percent' => null,
                    ];
                }

                continue;
            }

            $current = $metricValues[$threshold['metric']] ?? 0;
            $remaining = $target - $current;

            if ($remaining <= 0) {
                continue;
            }

            $isNear = $remaining <= max(3, ceil($target * 0.3));

            if ($isNear) {
                $unit = match ($threshold['metric']) {
                    'weight_lost' => 'kg',
                    'weight_entries' => 'pesées',
                    'total_activities' => 'activités',
                    'streak' => 'jours',
                    default => '',
                };

                $nearAchievements[] = [
                    'code' => $code,
                    'name' => $achievement->name,
                    'current' => $current,
                    'target' => $target,
                    'remaining' => $remaining,
                    'unit' => $unit,
                    'progress_percent' => round(($current / $target) * 100),
                ];
            }
        }

        usort($nearAchievements, fn ($a, $b) => ($b['progress_percent'] ?? 100) <=> ($a['progress_percent'] ?? 100));

        return array_slice($nearAchievements, 0, 3);
    }

    public function getAchievementCatalog(): array
    {
        return Achievement::orderBy('category')
            ->orderBy('points')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'code' => $a->code,
                'name' => $a->name,
                'description' => $a->description,
                'icon' => $a->icon,
                'points' => $a->points,
                'category' => $a->category,
            ])
            ->toArray();
    }
}
