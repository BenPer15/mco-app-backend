<?php

namespace App\Domains\Tracking\Data;

use App\Domains\Patient\ValueObjects\Patient;
use DateTimeImmutable;

class PatientDayData
{
  public function __construct(
    private Patient $patient,
    private DateTimeImmutable $date
  ) {}

  public function fromArray(array $data): self
  {
    return new self(
      patient: new Patient($data['patient']),
      date: $data['date']
    );
  }

  public function toArray(): array
  {
    return [
      'patient' => $this->patient->getUser(),
      'date' => $this->date
    ];
  }
}
