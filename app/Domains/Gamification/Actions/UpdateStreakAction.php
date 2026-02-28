<?php

namespace App\Domains\Gamification\Actions;

use App\Domains\Gamification\Models\PatientPoint;
use App\Domains\Patient\Models\Patient;
use Carbon\Carbon;

class UpdateStreakAction
{
    public function execute(Patient $patient): PatientPoint
    {
        $points = PatientPoint::firstOrCreate(
            ['patient_id' => $patient->id],
            ['total_points' => 0, 'current_streak_days' => 0, 'longest_streak_days' => 0]
        );

        $today = Carbon::today();
        $lastActivity = $points->last_activity_date
            ? Carbon::parse($points->last_activity_date)->startOfDay()
            : null;

        if ($lastActivity === null) {
            $points->current_streak_days = 1;
        } elseif ($lastActivity->isSameDay($today)) {
            return $points;
        } elseif ($lastActivity->isSameDay($today->copy()->subDay())) {
            $points->current_streak_days += 1;
        } else {
            $points->current_streak_days = 1;
        }

        if ($points->current_streak_days > $points->longest_streak_days) {
            $points->longest_streak_days = $points->current_streak_days;
        }

        $points->last_activity_date = $today;
        $points->save();

        return $points;
    }
}
