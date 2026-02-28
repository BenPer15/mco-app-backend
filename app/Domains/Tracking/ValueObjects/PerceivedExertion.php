<?php

namespace App\Domains\Tracking\ValueObjects;

use App\Domains\Tracking\Enums\BorgRating;
use InvalidArgumentException;

final class PerceivedExertion
{
  private BorgRating $rating;

  public function __construct(int $value)
  {
    if (!in_array($value, BorgRating::values(), true)) {
      throw new InvalidArgumentException('Invalid Borg rating.');
    }

    $this->rating = BorgRating::from($value);
  }

  public function getValue(): int
  {
    return $this->rating->value;
  }

  public function name(): string
  {
    return $this->rating->name();
  }

  public function isHighIntensity(): bool
  {
    return $this->rating->value >= 15;
  }
}
