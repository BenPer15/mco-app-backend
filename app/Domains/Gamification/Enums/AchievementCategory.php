<?php

namespace App\Domains\Gamification\Enums;

use App\Domains\Shared\Enums\Enum;

enum AchievementCategory: string
{
  use Enum;

  case WEIGHT = 'weight';
  case ACTIVITY = 'activity';
  case STREAK = 'streak';
  case BMI = 'bmi';
  case CONSISTENCY = 'consistency';
  case NUTRITION = 'nutrition';
  case STEPS = 'steps';

  public function name(): string
  {
    return match ($this) {
      self::WEIGHT => 'Poids',
      self::ACTIVITY => 'Activité',
      self::STREAK => 'Série',
      self::BMI => 'IMC',
      self::CONSISTENCY => 'Régularité',
      self::NUTRITION => 'Nutrition',
      self::STEPS => 'Pas',
    };
  }

  public function icon(): string
  {
    return match ($this) {
      self::WEIGHT => 'lucide:scale',
      self::ACTIVITY => 'lucide:dumbbell',
      self::STREAK => 'lucide:flame',
      self::BMI => 'lucide:heart-pulse',
      self::CONSISTENCY => 'lucide:calendar-check',
      self::NUTRITION => 'lucide:utensils',
      self::STEPS => 'material-symbols:steps',
    };
  }
}
