<?php

namespace App\Domains\Gamification\Listeners;

use App\Domains\Gamification\Actions\AwardPointsAction;
use App\Domains\Gamification\Actions\UpdateStreakAction;
use App\Domains\Gamification\Enums\PointAction;
use App\Domains\Gamification\Services\AchievementCheckerService;
use App\Domains\Tracking\Events\WeightRecorded;

class HandleWeightRecorded
{
    public function __construct(
        private AwardPointsAction $awardPoints,
        private UpdateStreakAction $updateStreak,
        private AchievementCheckerService $achievementChecker,
    ) {}

    public function handle(WeightRecorded $event): void
    {
        $patient = $event->patient;

        $this->awardPoints->execute($patient, PointAction::RECORD_WEIGHT);

        $pointRecord = $this->updateStreak->execute($patient);

        $this->achievementChecker->checkAfterWeightRecorded($patient);

        $this->achievementChecker->checkStreakAchievements($patient, $pointRecord->current_streak_days);
    }
}
