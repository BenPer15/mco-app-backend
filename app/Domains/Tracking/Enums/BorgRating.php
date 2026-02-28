<?php

namespace App\Domains\Tracking\Enums;

use App\Domains\Shared\Enums\Enum;

enum BorgRating: int
{
  use Enum;

  case N0_EFFORT = 6;
  case VERY_VERY_LIGHT = 7;
  case VERY_LIGHT = 8;
  case VERY_LIGHT_2 = 9;
  case VERY_LIGHT_3 = 10;
  case LIGHT = 11;
  case OPTIMAL_TRAINING = 12;
  case SOMEWHAT_HARD = 13;
  case HARD = 14;
  case HARD_2 = 15;
  case VERY_HARD = 16;
  case VERY_HARD_2 = 17;
  case VERY_HARD_3 = 18;
  case EXTREMELY_HARD = 19;
  case MAXIMAL = 20;

  public function name(): string
  {
    return match ($this) {
      self::N0_EFFORT => 'Aucun effort',
      self::VERY_VERY_LIGHT => 'Extrêmement facile',
      self::VERY_LIGHT,
      self::VERY_LIGHT_2,
      self::VERY_LIGHT_3 => 'Très facile',
      self::LIGHT => 'Facile',
      self::OPTIMAL_TRAINING => 'Zone d\'entraînement optimal',
      self::SOMEWHAT_HARD => 'Moyennement difficile',
      self::HARD,
      self::HARD_2 => 'Difficile',
      self::VERY_HARD,
      self::VERY_HARD_2,
      self::VERY_HARD_3 => 'Très difficile',
      self::EXTREMELY_HARD => 'Extrêmement difficile',
      self::MAXIMAL => 'Exténuant',
    };
  }

  public function color(): string
  {
    return match ($this) {
      self::N0_EFFORT,
      self::VERY_VERY_LIGHT,
      self::VERY_LIGHT,
      self::VERY_LIGHT_2,
      self::VERY_LIGHT_3 => 'emerald',
      self::LIGHT,
      self::OPTIMAL_TRAINING,
      self::SOMEWHAT_HARD,
      self::HARD,
      self::HARD_2 => 'orange',
      self::VERY_HARD,
      self::VERY_HARD_2,
      self::VERY_HARD_3,
      self::EXTREMELY_HARD,
      self::MAXIMAL => 'red',
    };
  }
}
