<?php

namespace Database\Factories;

use App\Domains\Patient\Enums\Gender;
use App\Domains\Patient\Enums\SurgeryType;
use App\Domains\Patient\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory  extends Factory
{

  protected $model = Patient::class;

  public function definition(): array
  {
    return [
      'user_id' => null,
      'first_name' => $this->faker->firstName(),
      'last_name' => $this->faker->lastName(),
      'birth_date' => $this->faker->date(),
      'gender' => $this->faker->randomElement(Gender::cases())->value,
      'surgery_type' => $this->faker->randomElement(SurgeryType::cases())->value,
      'surgery_date' => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
      'height_cm' => $this->faker->numberBetween(140, 200),
    ];
  }
}
