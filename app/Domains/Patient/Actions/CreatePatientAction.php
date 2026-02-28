<?php

namespace App\Domains\Patient\Actions;

use App\Domains\Patient\Data\PatientData;
use App\Domains\Patient\Models\Patient;

class CreatePatientAction
{
  public function execute(PatientData $data): void
  {
    Patient::create($data->toArray());
  }
}
