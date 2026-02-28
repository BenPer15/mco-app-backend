<?php

namespace App\Domains\Gamification\Services;

use App\Domains\Gamification\Data\GamificationSummaryData;
use App\Domains\Gamification\Models\Achievement;
use App\Domains\Gamification\Models\PatientAchievement;
use App\Domains\Gamification\Models\PatientPoint;
use App\Domains\Patient\Models\Patient;

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
