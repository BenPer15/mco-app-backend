<?php

namespace App\Domains\Tracking\ValueObjects;

use App\Domains\Tracking\Enums\IntensityLevel as EnumsIntensityLevel;

final class IntensityLevel
{
  private EnumsIntensityLevel $intensity;

  public function __construct(string $value)
  {
    if (!in_array($value, EnumsIntensityLevel::values(), true)) {
      throw new \InvalidArgumentException('Invalid intensity level.');
    }

    $this->intensity = EnumsIntensityLevel::from($value);
  }

  public function getValue(): string
  {
    return $this->intensity->value;
  }

  public function name(): string
  {
    return $this->intensity->name();
  }
}
