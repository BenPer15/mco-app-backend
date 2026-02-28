<?php

namespace App\Domains\Gamification\Enums;

use App\Domains\Shared\Enums\Enum;

enum PointAction: string
{
  use Enum;

  case RECORD_WEIGHT = 'record_weight';
  case RECORD_ACTIVITY = 'record_activity';
  case RECORD_NUTRITION = 'record_nutrition';
  case RECORD_STEPS = 'record_steps';
  case COMPLETE_WEEKLY_REVIEW = 'complete_weekly_review';
  case STREAK_BONUS = 'streak_bonus';
  case ACHIEVEMENT_UNLOCKED = 'achievement_unlocked';

  public function name(): string
  {
    return match ($this) {
      self::RECORD_WEIGHT => 'Enregistrement de poids',
      self::RECORD_ACTIVITY => 'Enregistrement d\'activité',
      self::RECORD_NUTRITION => 'Enregistrement nutrition',
      self::RECORD_STEPS => 'Enregistrement de pas',
      self::COMPLETE_WEEKLY_REVIEW => 'Bilan hebdomadaire',
      self::STREAK_BONUS => 'Bonus de série',
      self::ACHIEVEMENT_UNLOCKED => 'Succès débloqué',
    };
  }

  public function points(): int
  {
    return match ($this) {
      self::RECORD_WEIGHT => 10,
      self::RECORD_ACTIVITY => 15,
      self::RECORD_NUTRITION => 10,
      self::RECORD_STEPS => 5,
      self::COMPLETE_WEEKLY_REVIEW => 25,
      self::STREAK_BONUS => 5,
      self::ACHIEVEMENT_UNLOCKED => 0,
    };
  }
}
