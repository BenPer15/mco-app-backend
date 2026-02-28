<?php

namespace App\Domains\Tracking\Data;

use App\Domains\Tracking\Enums\ActivityType;
use App\Domains\Tracking\ValueObjects\IntensityLevel;
use App\Domains\Tracking\ValueObjects\PerceivedExertion;
use Carbon\Carbon;
use DomainException;

class ActivityEntryData
{
  public function __construct(
    public ActivityType $activityType,
    public int $durationMinutes,
    public IntensityLevel $intensityLevel,
    public ?PerceivedExertion $borgRating = null,
    public ?int $numberOfSteps = null,
    public ?string $notes = null,
    public ?Carbon $recordedAt = null,
  ) {
    $this->recordedAt = $recordedAt ?? now();
    if ($durationMinutes <= 0) {
      throw new DomainException('Duration must be positive.');
    }
  }

  public static function fromArray(array $data): self
  {
    ray($data);
    return new self(
      activityType: isset($data['activity_type']) ? ActivityType::from($data['activity_type']) : null,
      durationMinutes: $data['duration_minutes'],
      intensityLevel: new IntensityLevel($data['intensity_level']),
      borgRating: isset($data['borg_rating']) ? new PerceivedExertion($data['borg_rating']) : null,
      numberOfSteps: $data['number_of_steps'] ?? null,
      notes: $data['notes'] ?? null,
      recordedAt: isset($data['recorded_at'])
        ? Carbon::parse($data['recorded_at'])
        : null,
    );
  }

  public function toArray(): array
  {
    return [
      'activity_type' => $this->activityType->value,
      'duration_minutes' => $this->durationMinutes,
      'intensity_level' => $this->intensityLevel->getValue(),
      'borg_rating' => $this->borgRating?->getValue(),
      'number_of_steps' => $this->numberOfSteps,
      'notes' => $this->notes,
      'recorded_at' => $this->recordedAt,
    ];
  }
}
