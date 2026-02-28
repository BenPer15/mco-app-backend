<?php

namespace App\Domains\Patient\Enums;

use App\Domains\Shared\Enums\Enum;

enum Gender: string
{
  use Enum;

  case MALE = 'male';
  case FEMALE = 'female';
  case OTHER = 'other';


  public function name(): string
  {
    return match($this) {
      self::MALE  => 'Homme',
      self::FEMALE => 'Femme',
      self::OTHER => 'Autre',
    };
  }
}
