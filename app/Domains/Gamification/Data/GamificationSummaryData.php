<?php

namespace App\Domains\Gamification\Data;

use App\Domains\Gamification\Models\PatientPoint;
use Illuminate\Support\Collection;

class GamificationSummaryData
{
    public function __construct(
        public int $totalPoints,
        public int $currentStreakDays,
        public int $longestStreakDays,
        public ?string $lastActivityDate,
        public array $recentAchievements,
        public int $totalAchievements,
        public int $unlockedAchievements,
    ) {}

    public static function fromModels(
        PatientPoint $points,
        Collection $recentAchievements,
        int $totalCount,
        int $unlockedCount,
    ): self {
        return new self(
            totalPoints: $points->total_points,
            currentStreakDays: $points->current_streak_days,
            longestStreakDays: $points->longest_streak_days,
            lastActivityDate: $points->last_activity_date?->toISOString(),
            recentAchievements: $recentAchievements->toArray(),
            totalAchievements: $totalCount,
            unlockedAchievements: $unlockedCount,
        );
    }

    public function toArray(): array
    {
        return [
            'total_points' => $this->totalPoints,
            'current_streak_days' => $this->currentStreakDays,
            'longest_streak_days' => $this->longestStreakDays,
            'last_activity_date' => $this->lastActivityDate,
            'recent_achievements' => $this->recentAchievements,
            'total_achievements' => $this->totalAchievements,
            'unlocked_achievements' => $this->unlockedAchievements,
        ];
    }
}
