<?php

namespace App\Domains\Tracking\Enums;

enum ActivityType: string
{
  case WALKING = 'walking';
  case RUNNING = 'running';
  case CYCLING = 'cycling';
  case SWIMMING = 'swimming';
  case TREADMILL = 'treadmill';
  case ELLIPTICAL = 'elliptical';
  case STRENGTH_TRAINING = 'strength_training';
  case YOGA = 'yoga';
  case OTHER = 'other';

  public function label(): string
  {
    return match ($this) {
      self::WALKING => 'Marche',
      self::RUNNING => 'Course',
      self::CYCLING => 'Vélo',
      self::SWIMMING => 'Natation',
      self::TREADMILL => 'Tapis de course',
      self::ELLIPTICAL => 'Vélo elliptique',
      self::STRENGTH_TRAINING => 'Musculation',
      self::YOGA => 'Yoga',
      self::OTHER => 'Autre',
    };
  }
}
