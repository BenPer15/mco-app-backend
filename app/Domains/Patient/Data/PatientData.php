<?php

namespace App\Domains\Patient\Data;

use App\Domains\Core\ValueObjects\User;
use App\Domains\Patient\Enums\Gender;
use App\Domains\Patient\Enums\SurgeryType;
use DateTimeImmutable;

class PatientData
{
  public function __construct(
    public User $user_id,
    public string $first_name,
    public string $last_name,
    public DateTimeImmutable $birth_date,
    public Gender $gender,
    public int $height_cm,
    public SurgeryType $surgery_type,
    public DateTimeImmutable $surgery_date,
  ) {}

  public static function fromArray(array $data): self
  {
    return new self(
      user_id: User::fromString($data['user_id']),
      first_name: $data['first_name'],
      last_name: $data['last_name'],
      birth_date: $data['birth_date'],
      gender: Gender::from($data['gender']),
      height_cm: $data['height_cm'],
      surgery_type: SurgeryType::from($data['surgery_type']),
      surgery_date: $data['surgery_date'],
    );
  }

  public function toArray(): array
  {
    return [
      'user_id' => $this->user_id->getId(),
      'birth_date' => $this->birth_date,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'gender' => $this->gender->value,
      'height_cm' => $this->height_cm,
      'surgery_type' => $this->surgery_type->value,
      'surgery_date' => $this->surgery_date,
    ];
  }
}
