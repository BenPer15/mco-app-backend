<?php

namespace App\Domains\Tracking\Services;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Actions\Steps\RecordStepsAction;
use App\Domains\Tracking\Models\StepEntry;
use App\Domains\Tracking\Models\PatientDay;
use Carbon\Carbon;

class StepTrackingService
{
    public function __construct(
        private RecordStepsAction $recordSteps
    ) {}

    public function recordSteps(Patient $patient, array $data): StepEntry
    {
        return $this->recordSteps->execute(
            patient: $patient,
            steps: $data['steps'],
            recordedAt: $data['recorded_at'] ?? null,
        );
    }

    public function getDaySteps(Patient $patient, ?string $date = null): ?StepEntry
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : today()->toDateString();

        $patientDay = PatientDay::where('patient_id', $patient->id)
            ->where('date', $targetDate)
            ->first();

        if (! $patientDay) {
            return null;
        }

        return $patientDay->stepEntry;
    }
}
