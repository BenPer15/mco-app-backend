<?php

namespace App\Domains\Gamification\Listeners;

use App\Domains\Gamification\Actions\AwardPointsAction;
use App\Domains\Gamification\Actions\UpdateStreakAction;
use App\Domains\Gamification\Enums\PointAction;
use App\Domains\Gamification\Services\AchievementCheckerService;
use App\Domains\Tracking\Events\ActivityRecorded;

class HandleActivityRecorded
{
    public function __construct(
        private AwardPointsAction $awardPoints,
        private UpdateStreakAction $updateStreak,
        private AchievementCheckerService $achievementChecker,
    ) {}

    public function handle(ActivityRecorded $event): void
    {
        $patient = $event->patient;

        $this->awardPoints->execute($patient, PointAction::RECORD_ACTIVITY);

        $pointRecord = $this->updateStreak->execute($patient);

        if ($pointRecord->current_streak_days > 1) {
            $this->awardPoints->execute($patient, PointAction::STREAK_BONUS);
        }

        $this->achievementChecker->checkAfterActivityRecorded($patient, $event->activityEntry);

        $this->achievementChecker->checkStreakAchievements($patient, $pointRecord->current_streak_days);
    }
}
