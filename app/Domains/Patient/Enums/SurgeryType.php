<?php

namespace App\Domains\Patient\Enums;

use App\Domains\Shared\Enums\Enum;

enum SurgeryType: string
{
  use Enum;

  case SLEEVE = 'sleeve';
  case BYPASS = 'bypass';
  case OTHER = 'other';

  public function name(): string
  {
    return match($this) {
      self::SLEEVE => 'Sleeve Gastrectomy',
      self::BYPASS => 'Gastric Bypass',
      self::OTHER => 'Other',
    };
  }
}
