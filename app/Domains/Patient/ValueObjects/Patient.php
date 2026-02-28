<?php

namespace App\Domains\Patient\ValueObjects;

use App\Domains\Patient\Models\Patient as ModelsPatient;

final class Patient
{
  private ModelsPatient $patient;

  public function __construct(private readonly string $value)
  {
    $this->validate();
    $this->patient = $this->findPatient();
  }

  public function getUser(): ModelsPatient
  {
    return $this->patient;
  }

  public function getId(): string
  {
    return $this->patient->id;
  }

  public function __toString(): string
  {
    return $this->getId();
  }

  public static function fromString(string $value): self
  {
    return new self($value);
  }

  public function validate(): void
  {
    if (empty(trim($this->value))) {
      throw new \InvalidArgumentException('User identifier cannot be empty.');
    }
  }

  private function findPatient(): ModelsPatient
  {
    try {
      return ModelsPatient::findOrFail($this->value);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      throw new \InvalidArgumentException("User with ID {$this->value} not found.", 0, $e);
    }
  }
}
