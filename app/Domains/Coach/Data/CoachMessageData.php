<?php

namespace App\Domains\Coach\Data;

final class CoachMessageData
{
    public function __construct(
        public string $message,
        public string $tip,
        public string $icon,
        public string $mood,
        public string $source = 'ai',
        public string $zephState = 'idle',
    ) {}

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'tip' => $this->tip,
            'icon' => $this->icon,
            'mood' => $this->mood,
            'source' => $this->source,
            'zeph_state' => $this->zephState,
        ];
    }
}
