<?php

namespace App\Domains\Tracking\Enums;

use App\Domains\Shared\Enums\Enum;

enum IntensityLevel: string
{
  use Enum;

  case LOW = 'low';
  case MEDIUM = 'medium';
  case HIGH = 'high';

  public function name(): string
  {
    return match ($this) {
      self::LOW => 'Légere',
      self::MEDIUM => 'Modérée',
      self::HIGH => 'Intense',
    };
  }
}
