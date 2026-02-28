<?php

namespace App\Domains\Gamification\Events;

use App\Domains\Gamification\Models\PatientAchievement;
use App\Domains\Patient\Models\Patient;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public PatientAchievement $patientAchievement,
        public Patient $patient,
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.' . $this->patient->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'achievement.unlocked';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $achievement = $this->patientAchievement->achievement;

        return [
            'id' => $this->patientAchievement->id,
            'code' => $achievement->code,
            'name' => $achievement->name,
            'description' => $achievement->description,
            'icon' => $achievement->icon,
            'points' => $achievement->points,
            'category' => $achievement->category->value,
            'unlocked_at' => $this->patientAchievement->unlocked_at->toISOString(),
        ];
    }
}
