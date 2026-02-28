<?php

namespace App\Domains\Tracking\Events;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\ActivityEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ActivityEntry $activityEntry,
        public Patient $patient
    ) {}
}
