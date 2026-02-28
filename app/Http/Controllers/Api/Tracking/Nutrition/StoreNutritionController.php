<?php

namespace App\Http\Controllers\Api\Tracking\Nutrition;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\NutritionTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreNutritionController extends Controller
{
    public function __construct(
        private NutritionTrackingService $nutritionService
    ) {}

    public function __invoke(Patient $patient, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'proteins_ok' => ['required', 'boolean'],
            'vegetables_ok' => ['required', 'boolean'],
            'hydration_ok' => ['required', 'boolean'],
            'texture_ok' => ['required', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recorded_at' => ['nullable', 'date'],
        ]);

        $entry = $this->nutritionService->recordNutrition($patient, $validated);

        return response()->json([
            'message' => 'Nutrition enregistrée avec succès',
            'data' => $entry,
        ], 201);
    }
}
