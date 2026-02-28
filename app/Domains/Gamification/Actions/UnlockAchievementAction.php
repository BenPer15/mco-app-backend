<?php

namespace App\Domains\Gamification\Actions;

use App\Domains\Gamification\Events\AchievementUnlocked;
use App\Domains\Gamification\Models\Achievement;
use App\Domains\Gamification\Models\PatientAchievement;
use App\Domains\Gamification\Models\PatientPoint;
use App\Domains\Patient\Models\Patient;

class UnlockAchievementAction
{
    public function execute(Patient $patient, string $achievementCode): ?PatientAchievement
    {
        $achievement = Achievement::where('code', $achievementCode)->first();

        if (!$achievement) {
            return null;
        }

        $existing = PatientAchievement::where('patient_id', $patient->id)
            ->where('achievement_id', $achievement->id)
            ->first();

        if ($existing) {
            return null;
        }

        $patientAchievement = PatientAchievement::create([
            'patient_id' => $patient->id,
            'achievement_id' => $achievement->id,
            'unlocked_at' => now(),
        ]);

        if ($achievement->points > 0) {
            $pointRecord = PatientPoint::firstOrCreate(
                ['patient_id' => $patient->id],
                ['total_points' => 0, 'current_streak_days' => 0, 'longest_streak_days' => 0]
            );
            $pointRecord->increment('total_points', $achievement->points);
        }

        AchievementUnlocked::dispatch($patientAchievement, $patient);

        return $patientAchievement;
    }
}
