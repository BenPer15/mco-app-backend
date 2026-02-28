<?php

namespace App\Domains\Patient\Utils;

class BmiCalculator
{

  /**
   * Calculate BMI from weight and height
   *
   * @param float $weightKg Weight in kilograms
   * @param float $heightM Height in meters
   * @return float BMI value
   */
  public static function calculate(float $weightKg, float $heightCm): float
  {
    if ($heightCm <= 0) {
      throw new \InvalidArgumentException('Height must be greater than 0');
    }

    $heightM = $heightCm / 100;
    return round($weightKg / ($heightM ** 2), 2);
  }
}
