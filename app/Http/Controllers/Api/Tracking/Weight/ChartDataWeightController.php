<?php

namespace App\Http\Controllers\Api\Tracking\Weight;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\WeightTrackingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ChartDataWeightController extends Controller
{
    public function __construct(
        private WeightTrackingService $weightService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient): JsonResponse
    {
        $days = request()->query('days', 30);
        $chartData = $this->weightService->getWeightChartData($patient, $days);

        return response()->json([
            'data' => $chartData,
        ]);
    }
}
