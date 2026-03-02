<?php

namespace App\Domains\Tracking\Events;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\StepEntry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StepsRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public StepEntry $stepEntry,
        public Patient $patient
    ) {}
}
