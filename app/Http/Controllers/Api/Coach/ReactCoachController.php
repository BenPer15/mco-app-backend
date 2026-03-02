<?php

namespace App\Http\Controllers\Api\Coach;

use App\Domains\Coach\Services\CoachService;
use App\Domains\Patient\Models\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReactCoachController extends Controller
{
    public function __construct(
        private CoachService $coachService
    ) {}

    public function __invoke(Request $request, Patient $patient): JsonResponse
    {
        $request->validate([
            'event_type' => 'required|string|in:activity_recorded,nutrition_recorded,weight_recorded,steps_recorded',
            'event_summary' => 'required|string|max:200',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
        ]);

        $message = $this->coachService->generateReaction(
            patient: $patient,
            eventType: $request->string('event_type'),
            eventSummary: $request->string('event_summary'),
            lat: $request->float('lat') ?: null,
            lng: $request->float('lng') ?: null,
        );

        return response()->json([
            'data' => $message->toArray(),
        ]);
    }
}
