<?php

namespace App\Domains\Gamification\Services;

use App\Domains\Gamification\Actions\UnlockAchievementAction;
use App\Domains\Gamification\Models\PatientAchievement;
use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\ActivityEntry;
use App\Domains\Tracking\Models\PatientDay;
use App\Domains\Tracking\Models\WeightEntry;

class AchievementCheckerService
{
    public function __construct(
        private UnlockAchievementAction $unlockAchievement,
    ) {}

    public function checkAfterWeightRecorded(Patient $patient): array
    {
        $unlocked = [];

        $weightEntries = WeightEntry::where('patient_id', $patient->id)
            ->orderBy('recorded_at')
            ->get();

        if ($weightEntries->count() >= 1) {
            $unlocked[] = $this->tryUnlock($patient, 'first_weight');
        }

        if ($weightEntries->count() >= 10) {
            $unlocked[] = $this->tryUnlock($patient, 'weight_10_entries');
        }

        if ($weightEntries->count() >= 2) {
            $startingWeight = $weightEntries->first()->weight_kg;
            $currentWeight = $weightEntries->last()->weight_kg;
            $lost = $startingWeight - $currentWeight;

            $milestones = [
                5 => 'weight_5kg_lost',
                10 => 'weight_10kg_lost',
                20 => 'weight_20kg_lost',
                30 => 'weight_30kg_lost',
                50 => 'weight_50kg_lost',
            ];

            foreach ($milestones as $threshold => $code) {
                if ($lost >= $threshold) {
                    $unlocked[] = $this->tryUnlock($patient, $code);
                }
            }
        }

        $latestEntry = $weightEntries->last();
        if ($latestEntry && $latestEntry->bmi) {
            $bmi = (float) $latestEntry->bmi;
            $bmiMilestones = [
                40 => 'bmi_below_40',
                35 => 'bmi_below_35',
                30 => 'bmi_below_30',
                25 => 'bmi_below_25',
            ];

            foreach ($bmiMilestones as $threshold => $code) {
                if ($bmi < $threshold) {
                    $unlocked[] = $this->tryUnlock($patient, $code);
                }
            }
        }

        return array_filter($unlocked);
    }

    public function checkAfterActivityRecorded(Patient $patient, ?ActivityEntry $activityEntry = null): array
    {
        $unlocked = [];

        $totalActivities = ActivityEntry::whereHas('patientDay', function ($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })->count();

        if ($totalActivities >= 1) {
            $unlocked[] = $this->tryUnlock($patient, 'first_activity');
        }

        $activityMilestones = [
            10 => 'activity_10',
            25 => 'activity_25',
            50 => 'activity_50',
            100 => 'activity_100',
        ];

        foreach ($activityMilestones as $threshold => $code) {
            if ($totalActivities >= $threshold) {
                $unlocked[] = $this->tryUnlock($patient, $code);
            }
        }

        if ($activityEntry && $activityEntry->duration_minutes >= 60) {
            $unlocked[] = $this->tryUnlock($patient, 'activity_60min');
        }

        return array_filter($unlocked);
    }

    public function checkStreakAchievements(Patient $patient, int $currentStreak): array
    {
        $unlocked = [];

        $streakMilestones = [
            3 => 'streak_3',
            7 => 'streak_7',
            14 => 'streak_14',
            30 => 'streak_30',
            60 => 'streak_60',
            90 => 'streak_90',
        ];

        foreach ($streakMilestones as $threshold => $code) {
            if ($currentStreak >= $threshold) {
                $unlocked[] = $this->tryUnlock($patient, $code);
            }
        }

        $unlocked[] = $this->checkPerfectWeek($patient);

        return array_filter($unlocked);
    }

    private function checkPerfectWeek(Patient $patient): ?PatientAchievement
    {
        $startOfWeek = now()->startOfWeek();
        $daysWithActivity = PatientDay::where('patient_id', $patient->id)
            ->whereBetween('date', [$startOfWeek, $startOfWeek->copy()->endOfWeek()])
            ->whereHas('activities')
            ->count();

        if ($daysWithActivity >= 7) {
            return $this->tryUnlock($patient, 'perfect_week');
        }

        return null;
    }

    private function tryUnlock(Patient $patient, string $code): ?PatientAchievement
    {
        return $this->unlockAchievement->execute($patient, $code);
    }
}
