<?php

namespace App\Http\Controllers\Api\Tracking\Activity;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\ActivityTrackingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\StoreActivityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrentWeekActivityController extends Controller
{
    public function __construct(
        private ActivityTrackingService $trackingService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient): JsonResponse
    {
        $entry = $this->trackingService->getCurrentWeekActivities($patient);

        return response()->json([
            'message' => 'Activité de la semaine en cours récupérée avec succès',
            'data' => $entry,
        ], 201);
    }
}
