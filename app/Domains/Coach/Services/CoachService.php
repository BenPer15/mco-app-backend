<?php

namespace App\Domains\Coach\Services;

use Anthropic\Client;
use App\Domains\Coach\Data\CoachMessageData;
use App\Domains\Coach\Models\CoachMessage;
use App\Domains\Gamification\Models\PatientAchievement;
use App\Domains\Gamification\Models\PatientPoint;
use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\PatientDay;
use Illuminate\Support\Facades\Log;

class CoachService
{
    public function __construct(
        private WeatherService $weatherService,
        private PatientContextBuilder $contextBuilder,
        private PromptBuilder $promptBuilder,
    ) {}

    public function getOrGenerateMessage(
        Patient $patient,
        ?float $lat = null,
        ?float $lng = null,
        bool $forceRefresh = false,
    ): CoachMessageData {
        $today = now()->toDateString();
        $zephState = $this->computeZephState($patient);

        if (! $forceRefresh) {
            $cached = CoachMessage::where('patient_id', $patient->id)
                ->where('date', $today)
                ->first();

            if ($cached) {
                return new CoachMessageData(
                    message: $cached->message,
                    tip: $cached->tip ?? '',
                    icon: $cached->icon,
                    mood: $cached->mood,
                    source: $cached->source,
                    zephState: $zephState,
                );
            }
        }

        try {
            $messageData = $this->generateAiMessage($patient, $lat, $lng);
        } catch (\Throwable $e) {
            Log::error('Coach AI generation failed, falling back to rules', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
            ]);
            $messageData = $this->generateFallbackMessage($patient);
        }

        $messageData = new CoachMessageData(
            message: $messageData->message,
            tip: $messageData->tip,
            icon: $messageData->icon,
            mood: $messageData->mood,
            source: $messageData->source,
            zephState: $zephState,
        );

        CoachMessage::updateOrCreate(
            [
                'patient_id' => $patient->id,
                'date' => $today,
            ],
            [
                'message' => $messageData->message,
                'tip' => $messageData->tip,
                'icon' => $messageData->icon,
                'mood' => $messageData->mood,
                'source' => $messageData->source,
            ]
        );

        return $messageData;
    }

    public function generateReaction(
        Patient $patient,
        string $eventType,
        string $eventSummary,
        ?float $lat = null,
        ?float $lng = null,
    ): CoachMessageData {
        $today = now()->toDateString();

        $weather = ($lat && $lng)
            ? $this->weatherService->getWeather($lat, $lng)
            : null;

        $context = $this->contextBuilder->build($patient, $weather);

        $systemPrompt = $this->promptBuilder->buildSystemPrompt();
        $userPrompt = $this->promptBuilder->buildReactionPrompt($context, $eventType, $eventSummary);

        try {
            $client = new Client(apiKey: config('services.anthropic.api_key'));

            $response = $client->messages->create(
                maxTokens: 256,
                messages: [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                model: config('services.anthropic.model', 'claude-sonnet-4-20250514'),
                system: $systemPrompt,
            );

            $content = $response->content[0]->text;
            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);

            $parsed = json_decode($content, true);

            if (! $parsed || ! isset($parsed['message'])) {
                throw new \RuntimeException('Invalid AI reaction format: ' . $content);
            }

            $messageData = new CoachMessageData(
                message: $parsed['message'],
                tip: $parsed['tip'] ?? '',
                icon: $parsed['icon'] ?? 'lucide:flame',
                mood: $parsed['mood'] ?? 'encouraging',
                source: 'ai',
            );
        } catch (\Throwable $e) {
            Log::error('Coach AI reaction failed, falling back', [
                'patient_id' => $patient->id,
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
            $messageData = $this->generateFallbackReaction($patient, $eventType);
        }

        $zephState = $this->computeZephState($patient);

        $messageData = new CoachMessageData(
            message: $messageData->message,
            tip: $messageData->tip,
            icon: $messageData->icon,
            mood: $messageData->mood,
            source: $messageData->source,
            zephState: $zephState,
        );

        CoachMessage::updateOrCreate(
            [
                'patient_id' => $patient->id,
                'date' => $today,
            ],
            [
                'message' => $messageData->message,
                'tip' => $messageData->tip,
                'icon' => $messageData->icon,
                'mood' => $messageData->mood,
                'source' => $messageData->source,
            ]
        );

        return $messageData;
    }

    private function generateAiMessage(Patient $patient, ?float $lat, ?float $lng): CoachMessageData
    {
        $weather = ($lat && $lng)
            ? $this->weatherService->getWeather($lat, $lng)
            : null;

        $context = $this->contextBuilder->build($patient, $weather);

        $systemPrompt = $this->promptBuilder->buildSystemPrompt();
        $userPrompt = $this->promptBuilder->buildUserPrompt($context);

        $client = new Client(apiKey: config('services.anthropic.api_key'));

        $response = $client->messages->create(
            maxTokens: config('services.anthropic.max_tokens', 512),
            messages: [
                ['role' => 'user', 'content' => $userPrompt],
            ],
            model: config('services.anthropic.model', 'claude-sonnet-4-20250514'),
            system: $systemPrompt,
        );

        $content = $response->content[0]->text;

        // Strip potential markdown code fences
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        $parsed = json_decode($content, true);

        if (! $parsed || ! isset($parsed['message'])) {
            throw new \RuntimeException('Invalid AI response format: ' . $content);
        }

        return new CoachMessageData(
            message: $parsed['message'],
            tip: $parsed['tip'] ?? '',
            icon: $parsed['icon'] ?? 'lucide:flame',
            mood: $parsed['mood'] ?? 'encouraging',
            source: 'ai',
        );
    }

    private function generateFallbackMessage(Patient $patient): CoachMessageData
    {
        $messages = [
            [
                'message' => "Bonjour {$patient->first_name} ! Chaque jour est une nouvelle opportunité de progresser dans votre parcours. Continuez comme ça !",
                'tip' => 'Pensez à remplir votre suivi nutritionnel, cela ne prend que 30 secondes.',
                'icon' => 'lucide:sunrise',
                'mood' => 'encouraging',
            ],
            [
                'message' => "Bonjour {$patient->first_name} ! Votre régularité fait toute la différence. Chaque petite action compte dans votre transformation.",
                'tip' => 'Une marche de 20 minutes après le repas aide la digestion et le moral.',
                'icon' => 'lucide:heart',
                'mood' => 'encouraging',
            ],
            [
                'message' => "{$patient->first_name}, vous êtes sur la bonne voie ! N'oubliez pas que la constance est la clé du succès post-opératoire.",
                'tip' => "Hydratez-vous bien : visez au moins 1,5L d'eau par jour.",
                'icon' => 'lucide:target',
                'mood' => 'gentle_push',
            ],
            [
                'message' => "Belle journée pour avancer, {$patient->first_name} ! Votre corps se transforme jour après jour grâce à vos efforts.",
                'tip' => 'Privilégiez les protéines à chaque repas pour préserver votre masse musculaire.',
                'icon' => 'lucide:flame',
                'mood' => 'energetic',
            ],
        ];

        $index = now()->day % count($messages);
        $selected = $messages[$index];

        return new CoachMessageData(
            message: $selected['message'],
            tip: $selected['tip'],
            icon: $selected['icon'],
            mood: $selected['mood'],
            source: 'fallback',
        );
    }

    private function generateFallbackReaction(Patient $patient, string $eventType): CoachMessageData
    {
        $reactions = [
            'activity_recorded' => [
                'message' => "Bravo {$patient->first_name} ! Chaque activité compte dans votre parcours. Continuez sur cette lancée !",
                'tip' => "N'oubliez pas de bien vous hydrater après l'effort.",
                'icon' => 'lucide:dumbbell',
                'mood' => 'celebrating',
            ],
            'nutrition_recorded' => [
                'message' => "Super {$patient->first_name} ! Votre suivi nutritionnel est un geste essentiel pour votre réussite.",
                'tip' => 'La régularité dans le suivi est la clé du succès.',
                'icon' => 'lucide:salad',
                'mood' => 'encouraging',
            ],
            'weight_recorded' => [
                'message' => "{$patient->first_name}, merci de suivre votre poids ! Chaque pesée vous rapproche de vos objectifs.",
                'tip' => 'Pesez-vous toujours au même moment pour des résultats cohérents.',
                'icon' => 'lucide:scale',
                'mood' => 'encouraging',
            ],
            'steps_recorded' => [
                'message' => "Bravo {$patient->first_name} ! Chaque pas compte dans votre parcours de santé. Continuez à bouger !",
                'tip' => "La marche régulière est l'un des meilleurs exercices post-opératoires.",
                'icon' => 'material-symbols:steps',
                'mood' => 'encouraging',
            ],
        ];

        $selected = $reactions[$eventType] ?? $reactions['activity_recorded'];

        return new CoachMessageData(
            message: $selected['message'],
            tip: $selected['tip'],
            icon: $selected['icon'],
            mood: $selected['mood'],
            source: 'fallback',
        );
    }

    public function computeZephState(Patient $patient): string
    {
        $today = now()->toDateString();

        $points = PatientPoint::where('patient_id', $patient->id)->first();
        $currentStreak = $points?->current_streak_days ?? 0;

        $patientDay = PatientDay::where('patient_id', $patient->id)
            ->where('date', $today)
            ->first();

        $hasActivityToday = $patientDay?->activities()->exists() ?? false;
        $hasNutritionToday = $patientDay?->nutritionEntries()->exists() ?? false;
        $hasStepsToday = $patientDay?->stepEntry()->exists() ?? false;
        $hasAnyActionToday = $hasActivityToday || $hasNutritionToday || $hasStepsToday;

        // Check if achievement unlocked today
        $achievementToday = PatientAchievement::where('patient_id', $patient->id)
            ->whereDate('unlocked_at', $today)
            ->exists();

        if ($achievementToday) {
            return 'celebrating';
        }

        if ($currentStreak >= 3 && $hasAnyActionToday) {
            return 'energetic';
        }

        if ($hasAnyActionToday) {
            return 'happy';
        }

        if ($currentStreak > 0) {
            return 'idle';
        }

        return 'sleeping';
    }
}
