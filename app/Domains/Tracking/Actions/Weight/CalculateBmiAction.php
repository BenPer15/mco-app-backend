<?php

namespace App\Domains\Tracking\Actions\Weight;

class CalculateBmiAction
{
  public function execute(float $weightKg, int $heightCm): float
  {
    if ($heightCm <= 0) {
      throw new \InvalidArgumentException('Height must be greater than 0');
    }

    $heightM = $heightCm / 100;
    return round($weightKg / ($heightM * $heightM), 2);
  }

  public function getCategory(float $bmi): string
  {
    return match (true) {
      $bmi < 18.5 => 'Maigreur',
      $bmi < 25 => 'Normal',
      $bmi < 30 => 'Surpoids',
      $bmi < 35 => 'Obésité modérée',
      $bmi < 40 => 'Obésité sévère',
      default => 'Obésité morbide',
    };
  }
}
