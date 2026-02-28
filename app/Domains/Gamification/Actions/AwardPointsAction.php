<?php

namespace App\Domains\Gamification\Actions;

use App\Domains\Gamification\Enums\PointAction;
use App\Domains\Gamification\Models\PatientPoint;
use App\Domains\Patient\Models\Patient;

class AwardPointsAction
{
    public function execute(Patient $patient, PointAction $action, int $multiplier = 1): PatientPoint
    {
        $points = PatientPoint::firstOrCreate(
            ['patient_id' => $patient->id],
            ['total_points' => 0, 'current_streak_days' => 0, 'longest_streak_days' => 0]
        );

        $pointsToAdd = $action->points() * $multiplier;

        $points->increment('total_points', $pointsToAdd);

        return $points->fresh();
    }
}
