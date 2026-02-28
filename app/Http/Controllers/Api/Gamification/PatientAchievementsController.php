<?php

namespace App\Http\Controllers\Api\Gamification;

use App\Domains\Gamification\Services\GamificationService;
use App\Domains\Patient\Models\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PatientAchievementsController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function __invoke(Patient $patient): JsonResponse
    {
        $achievements = $this->gamificationService->getPatientAchievements($patient);

        return response()->json([
            'data' => $achievements,
        ]);
    }
}
