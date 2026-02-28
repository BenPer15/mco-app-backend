<?php

namespace App\Domains\Tracking\Data;

use Carbon\Carbon;

final class WeightEntryData
{

  public function __construct(
    public float $weight,
    public ?string $notes = null,
    public ?Carbon $recordedAt = null,
  ) {
    $this->recordedAt = $recordedAt ?? now();
  }

  public static function fromArray(array $data): self
  {
    return new self(
      weight: $data['weight'],
      notes: $data['notes'] ?? null,
      recordedAt: isset($data['recorded_at'])
        ? Carbon::parse($data['recorded_at'])
        : null,
    );
  }

  public function toArray(): array
  {
    return [
      'weight' => $this->weight,
      'notes' => $this->notes,
      'recorded_at' => $this->recordedAt,
    ];
  }
}
