<?php

namespace App\Http\Controllers\Api\Tracking\Weight;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\WeightTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class LatestWeightController extends Controller
{
    public function __construct(
        private WeightTrackingService $weightService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient): JsonResponse
    {
        $latestWeight = $this->weightService->getLatestWeight($patient);

        return response()->json([
            'data' => $latestWeight,
        ]);
    }
}
