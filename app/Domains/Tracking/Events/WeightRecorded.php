<?php

namespace App\Domains\Tracking\Events;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\WeightEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WeightRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WeightEntry $weightEntry,
        public Patient $patient
    ) {}
}
