<?php

namespace App\Domains\Tracking\Actions\Activity;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Data\ActivityEntryData;
use App\Domains\Tracking\Events\ActivityRecorded;
use App\Domains\Tracking\Models\ActivityEntry;
use App\Domains\Tracking\Models\PatientDay;

class RecordActivityAction
{
  public function execute(Patient $patient, ActivityEntryData $data)
  {
    $patientDay = PatientDay::firstOrCreate([
      'patient_id' => $patient->id,
      'date' => $data->recordedAt->toDateString(),
    ]);

    $params = $data->toArray();

    $activity = ActivityEntry::create([
      'patient_day_id' => $patientDay->id,
      ...$params,
    ]);

    event(new ActivityRecorded($activity, $patient));

    return $activity;
  }
}
