<?php

namespace App\Domains\Tracking\Services;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\NutritionEntry;
use App\Domains\Tracking\Models\PatientDay;
use Carbon\Carbon;

class NutritionTrackingService
{
    public function recordNutrition(Patient $patient, array $data): NutritionEntry
    {
        $recordedAt = Carbon::parse($data['recorded_at'] ?? now());

        $patientDay = PatientDay::firstOrCreate([
            'patient_id' => $patient->id,
            'date' => $recordedAt->toDateString(),
        ]);

        return NutritionEntry::updateOrCreate(
            ['patient_day_id' => $patientDay->id],
            [
                'proteins_ok' => $data['proteins_ok'] ?? false,
                'vegetables_ok' => $data['vegetables_ok'] ?? false,
                'hydration_ok' => $data['hydration_ok'] ?? false,
                'texture_ok' => $data['texture_ok'] ?? false,
                'notes' => $data['notes'] ?? null,
                'recorded_at' => $recordedAt,
            ]
        );
    }

    public function getDayNutrition(Patient $patient, ?string $date = null): ?NutritionEntry
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : today()->toDateString();

        return NutritionEntry::whereHas('patientDay', function ($query) use ($patient, $targetDate) {
            $query->where('patient_id', $patient->id)
                ->where('date', $targetDate);
        })->first();
    }
}
