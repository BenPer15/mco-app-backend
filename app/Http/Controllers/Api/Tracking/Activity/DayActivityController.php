<?php

namespace App\Http\Controllers\Api\Tracking\Activity;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\ActivityTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DayActivityController extends Controller
{
    public function __construct(
        private ActivityTrackingService $trackingService
    ) {}

    public function __invoke(Patient $patient, Request $request): JsonResponse
    {
        $activities = $this->trackingService->getDayActivities($patient, $request->query('date'));

        return response()->json([
            'data' => $activities,
        ]);
    }
}
