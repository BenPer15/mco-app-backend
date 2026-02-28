<?php

namespace App\Domains\Shared\Enums;

trait Enum
{
  public function toArray(): array
  {
    $payload = [
      "display" => true,
      "value" => $this->value,
      "name" => $this->name(),
    ];

    if (method_exists($this, 'color')) {
      $payload['color'] = $this->color();
    }

    if (method_exists($this, 'icon')) {
      $payload['icon'] = $this->icon();
    }

    if (method_exists($this, 'display')) {
      $payload['display'] = $this->description();
    }

    if (method_exists($this, 'short')) {
      $payload['short'] = $this->description();
    }

    return $payload;
  }

  public static function map(): array
  {
    return collect(self::cases())
      ->map(fn($case) => $case->toArray())
      ->toArray();
  }

  public static function values(): array
  {
    return array_map(fn($case) => $case->value, self::cases());
  }
}
