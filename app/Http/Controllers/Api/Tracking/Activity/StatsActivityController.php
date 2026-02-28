<?php

namespace App\Http\Controllers\Api\Tracking\Activity;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\ActivityTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StatsActivityController extends Controller
{
    public function __construct(
        private ActivityTrackingService $trackingService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient): JsonResponse
    {
        $stats = $this->trackingService->getActivityStats($patient);

        return response()->json([
            'data' => $stats,
        ]);
    }
}
