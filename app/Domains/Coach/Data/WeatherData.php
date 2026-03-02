<?php

namespace App\Domains\Coach\Data;

final class WeatherData
{
    public function __construct(
        public string $description,
        public float $temperature,
        public float $feelsLike,
        public int $humidity,
        public ?float $windSpeed = null,
        public ?string $city = null,
    ) {}

    public static function fromApiResponse(array $data): self
    {
        return new self(
            description: $data['weather'][0]['description'] ?? 'inconnu',
            temperature: round($data['main']['temp'], 1),
            feelsLike: round($data['main']['feels_like'], 1),
            humidity: $data['main']['humidity'],
            windSpeed: isset($data['wind']['speed']) ? round($data['wind']['speed'], 1) : null,
            city: $data['name'] ?? null,
        );
    }

    public function toPromptString(): string
    {
        $parts = [
            "Météo actuelle : {$this->description}",
            "Température : {$this->temperature}°C (ressenti {$this->feelsLike}°C)",
            "Humidité : {$this->humidity}%",
        ];

        if ($this->windSpeed !== null) {
            $parts[] = "Vent : {$this->windSpeed} km/h";
        }

        if ($this->city) {
            $parts[] = "Localisation : {$this->city}";
        }

        return implode("\n", $parts);
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'temperature' => $this->temperature,
            'feels_like' => $this->feelsLike,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
            'city' => $this->city,
        ];
    }
}
