<?php

namespace App\Domains\Tracking\Actions\Steps;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Events\StepsRecorded;
use App\Domains\Tracking\Models\PatientDay;
use App\Domains\Tracking\Models\StepEntry;
use Carbon\Carbon;

class RecordStepsAction
{
    public function execute(Patient $patient, int $steps, ?string $recordedAt = null): StepEntry
    {
        $date = $recordedAt ? Carbon::parse($recordedAt) : now();

        $patientDay = PatientDay::firstOrCreate([
            'patient_id' => $patient->id,
            'date' => $date->toDateString(),
        ]);

        $stepEntry = StepEntry::updateOrCreate(
            ['patient_day_id' => $patientDay->id],
            [
                'steps' => $steps,
                'source' => 'manual',
                'recorded_at' => $date,
            ]
        );

        event(new StepsRecorded($stepEntry, $patient));

        return $stepEntry;
    }
}
