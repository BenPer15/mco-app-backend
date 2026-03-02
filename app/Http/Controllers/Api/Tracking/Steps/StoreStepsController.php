<?php

namespace App\Http\Controllers\Api\Tracking\Steps;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\StepTrackingService;
use App\Http\Requests\Tracking\StoreStepsRequest;
use Illuminate\Http\JsonResponse;

class StoreStepsController
{
    public function __construct(
        private StepTrackingService $stepService
    ) {}

    public function __invoke(Patient $patient, StoreStepsRequest $request): JsonResponse
    {
        $entry = $this->stepService->recordSteps($patient, $request->validated());

        return response()->json([
            'message' => 'Pas enregistrés avec succès',
            'data' => $entry,
        ], 201);
    }
}
