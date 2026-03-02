<?php

namespace App\Domains\Coach\Services;

use App\Domains\Coach\Data\WeatherData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    public function getWeather(float $lat, float $lng): ?WeatherData
    {
        $cacheKey = "weather:{$this->roundCoord($lat)}:{$this->roundCoord($lng)}";

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($lat, $lng) {
            try {
                $response = Http::timeout(5)->get(
                    config('services.openweathermap.base_url') . '/weather',
                    [
                        'lat' => $lat,
                        'lon' => $lng,
                        'appid' => config('services.openweathermap.api_key'),
                        'units' => 'metric',
                        'lang' => 'fr',
                    ]
                );

                if ($response->successful()) {
                    return WeatherData::fromApiResponse($response->json());
                }

                Log::warning('OpenWeatherMap API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (\Throwable $e) {
                Log::error('WeatherService error', ['error' => $e->getMessage()]);

                return null;
            }
        });
    }

    private function roundCoord(float $coord): string
    {
        return number_format(round($coord, 2), 2);
    }
}
