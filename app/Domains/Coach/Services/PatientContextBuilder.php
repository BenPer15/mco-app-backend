<?php

namespace App\Domains\Coach\Services;

use App\Domains\Coach\Data\PatientContextData;
use App\Domains\Coach\Data\WeatherData;
use App\Domains\Gamification\Services\GamificationService;
use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Models\PatientDay;
use App\Domains\Tracking\Models\WeeklyReview;
use App\Domains\Tracking\Services\ActivityTrackingService;
use App\Domains\Tracking\Services\NutritionTrackingService;
use App\Domains\Tracking\Services\WeightTrackingService;

class PatientContextBuilder
{
    public function __construct(
        private WeightTrackingService $weightService,
        private ActivityTrackingService $activityService,
        private NutritionTrackingService $nutritionService,
        private GamificationService $gamificationService,
    ) {}

    public function build(Patient $patient, ?WeatherData $weather = null): PatientContextData
    {
        $daysSinceSurgery = $patient->surgery_date
            ? (int) $patient->surgery_date->diffInDays(now())
            : null;

        $patientInfo = [
            'first_name' => $patient->first_name,
            'gender' => $patient->gender?->name() ?? 'Non renseigné',
            'age' => $patient->yo,
            'height_cm' => $patient->height_cm,
            'surgery_type' => $patient->surgery_type?->name() ?? 'Non renseigné',
            'surgery_date' => $patient->surgery_date?->format('d/m/Y') ?? 'Non renseignée',
            'days_since_surgery' => $daysSinceSurgery ?? 'Non renseigné',
            'objectives' => $patient->settings['objectives'] ?? null,
        ];

        $weightStats = $this->weightService->getWeightStats($patient);
        $activityStats = $this->activityService->getActivityStats($patient, 30);

        $todayNutrition = $this->nutritionService->getDayNutrition($patient);
        $todayNutritionArr = $todayNutrition ? [
            'proteins_ok' => $todayNutrition->proteins_ok,
            'vegetables_ok' => $todayNutrition->vegetables_ok,
            'hydration_ok' => $todayNutrition->hydration_ok,
            'texture_ok' => $todayNutrition->texture_ok,
        ] : null;

        $gamificationSummary = $this->gamificationService->getSummary($patient);

        $latestReview = WeeklyReview::where('patient_id', $patient->id)
            ->latest('created_at')
            ->first();

        $latestReviewArr = $latestReview ? [
            'physical_score' => $latestReview->physical_score,
            'mental_score' => $latestReview->mental_score,
            'adherence_score' => $latestReview->adherence_score,
            'comment' => $latestReview->comment,
        ] : null;

        // Today's progress
        $todayActivities = $this->activityService->getDayActivities($patient);
        $todaySteps = $this->getTodaySteps($patient);
        $objectives = $patient->settings['objectives'] ?? [];

        $todayProgress = [
            'steps' => [
                'current' => $todaySteps,
                'goal' => $objectives['steps'] ?? null,
                'percent' => isset($objectives['steps']) && $objectives['steps'] > 0
                    ? min(100, round(($todaySteps / $objectives['steps']) * 100))
                    : null,
            ],
            'activities' => [
                'count' => $todayActivities->count(),
                'total_minutes' => $todayActivities->sum('duration_minutes'),
                'goal_minutes' => $objectives['activities'] ?? null,
                'percent' => isset($objectives['activities']) && $objectives['activities'] > 0
                    ? min(100, round(($todayActivities->sum('duration_minutes') / $objectives['activities']) * 100))
                    : null,
                'types' => $todayActivities->pluck('activity_type')->map(fn ($t) => $t->value)->unique()->values()->toArray(),
            ],
            'nutrition' => [
                'filled' => $todayNutrition !== null,
                'pillars_completed' => $todayNutrition ? collect([
                    $todayNutrition->proteins_ok,
                    $todayNutrition->vegetables_ok,
                    $todayNutrition->hydration_ok,
                    $todayNutrition->texture_ok,
                ])->filter()->count() : 0,
                'pillars_total' => 4,
            ],
        ];

        // Near achievements
        $nearAchievements = $this->gamificationService->getAchievementProximity($patient);

        return new PatientContextData(
            patient: $patientInfo,
            weightStats: $weightStats,
            activityStats: $activityStats,
            todayNutrition: $todayNutritionArr,
            gamification: $gamificationSummary->toArray(),
            latestWeeklyReview: $latestReviewArr,
            weather: $weather,
            todayProgress: $todayProgress,
            nearAchievements: $nearAchievements,
        );
    }

    private function getTodaySteps(Patient $patient): int
    {
        $patientDay = PatientDay::where('patient_id', $patient->id)
            ->where('date', today()->toDateString())
            ->first();

        if (! $patientDay) {
            return 0;
        }

        return $patientDay->stepEntry?->steps ?? 0;
    }
}
