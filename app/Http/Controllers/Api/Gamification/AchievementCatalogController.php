<?php

namespace App\Http\Controllers\Api\Gamification;

use App\Domains\Gamification\Services\GamificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AchievementCatalogController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function __invoke(): JsonResponse
    {
        $catalog = $this->gamificationService->getAchievementCatalog();

        return response()->json([
            'data' => $catalog,
        ]);
    }
}
