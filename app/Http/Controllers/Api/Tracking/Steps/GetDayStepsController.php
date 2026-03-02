<?php

namespace App\Http\Controllers\Api\Tracking\Steps;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\StepTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetDayStepsController
{
    public function __construct(
        private StepTrackingService $stepService
    ) {}

    public function __invoke(Patient $patient, Request $request): JsonResponse
    {
        $entry = $this->stepService->getDaySteps(
            $patient,
            $request->query('date'),
        );

        return response()->json([
            'data' => $entry,
        ]);
    }
}
