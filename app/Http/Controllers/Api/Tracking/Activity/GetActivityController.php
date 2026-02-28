<?php

namespace App\Http\Controllers\Api\Tracking\Activity;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\ActivityTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetActivityController extends Controller
{
    public function __construct(
        private ActivityTrackingService $trackingService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient): JsonResponse
    {
        $days = request()->query('days', 30);
        $history = $this->trackingService->getActivityHistory($patient, $days);

        return response()->json([
            'data' => $history,
        ]);
    }
}
