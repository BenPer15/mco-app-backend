<?php

namespace App\Http\Controllers\Api\Coach;

use App\Domains\Coach\Services\CoachService;
use App\Domains\Patient\Models\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetCoachMessageController extends Controller
{
    public function __construct(
        private CoachService $coachService
    ) {}

    public function __invoke(Request $request, Patient $patient): JsonResponse
    {
        $request->validate([
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'refresh' => 'nullable|boolean',
        ]);

        $message = $this->coachService->getOrGenerateMessage(
            patient: $patient,
            lat: $request->float('lat') ?: null,
            lng: $request->float('lng') ?: null,
            forceRefresh: $request->boolean('refresh', false),
        );

        return response()->json([
            'data' => $message->toArray(),
        ]);
    }
}
