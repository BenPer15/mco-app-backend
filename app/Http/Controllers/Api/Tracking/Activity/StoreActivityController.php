<?php

namespace App\Http\Controllers\Api\Tracking\Activity;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\ActivityTrackingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tracking\StoreActivityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreActivityController extends Controller
{
    public function __construct(
        private ActivityTrackingService $trackingService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient, StoreActivityRequest $request): JsonResponse
    {
        $entry = $this->trackingService->recordActivity($patient, $request->validated());

        return response()->json([
            'message' => 'Activité enregistrée avec succès',
            'data' => $entry,
        ], 201);
    }
}
