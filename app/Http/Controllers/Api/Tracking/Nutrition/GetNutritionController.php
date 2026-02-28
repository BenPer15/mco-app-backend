<?php

namespace App\Http\Controllers\Api\Tracking\Nutrition;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\NutritionTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetNutritionController extends Controller
{
    public function __construct(
        private NutritionTrackingService $nutritionService
    ) {}

    public function __invoke(Patient $patient, Request $request): JsonResponse
    {
        $entry = $this->nutritionService->getDayNutrition(
            $patient,
            $request->query('date')
        );

        return response()->json([
            'data' => $entry,
        ]);
    }
}
