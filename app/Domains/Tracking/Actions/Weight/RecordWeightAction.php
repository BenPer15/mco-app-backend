<?php

namespace App\Domains\Tracking\Actions\Weight;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Data\WeightEntryData;
use App\Domains\Tracking\Events\WeightRecorded;
use App\Domains\Tracking\Models\WeightEntry;

class RecordWeightAction
{
  public function __construct(
    private CalculateBmiAction $calculateBmi
  ) {}

  public function execute(Patient $patient, WeightEntryData $data): WeightEntry
  {
    $bmi = null;
    if ($patient->height_cm) {
      $bmi = $this->calculateBmi->execute($data->weight, $patient->height_cm);
    }

    $entry = WeightEntry::create([
      'patient_id' => $patient->id,
      'weight_kg' => $data->weight,
      'bmi' => $bmi,
      'notes' => $data->notes,
      'recorded_at' => $data->recordedAt,
    ]);

    event(new WeightRecorded($entry, $patient));

    return $entry;
  }
}
