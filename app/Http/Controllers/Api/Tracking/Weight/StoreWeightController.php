<?php

namespace App\Http\Controllers\Api\Tracking\Weight;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\WeightTrackingService;
use App\Http\Requests\Tracking\StoreWeightRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreWeightController
{
    public function __construct(
        private WeightTrackingService $weightService
    ) {}

    public function __invoke(Patient $patient, StoreWeightRequest $request): JsonResponse
    {
        $entry = $this->weightService->recordWeight($patient, $request->validated());

        return response()->json([
            'message' => 'Poids enregistré avec succès',
            'data' => $entry,
        ], 201);
    }
}
