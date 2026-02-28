<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Patient\UploadAvatarController;
use App\Http\Controllers\Api\Patient\Tracking\GetPatientDayController;
use App\Http\Controllers\Api\Tracking\Activity\CurrentWeekActivityController;
use App\Http\Controllers\Api\Tracking\Activity\GetActivityController;
use App\Http\Controllers\Api\Tracking\Activity\StatsActivityController;
use App\Http\Controllers\Api\Tracking\Activity\StoreActivityController;
use App\Http\Controllers\Api\Tracking\Activity\DayActivityController;
use App\Http\Controllers\Api\Tracking\Weight\ChartDataWeightController;
use App\Http\Controllers\Api\Tracking\Weight\GetWeightController;
use App\Http\Controllers\Api\Tracking\Weight\LatestWeightController;
use App\Http\Controllers\Api\Tracking\Weight\StatsWeightController;
use App\Http\Controllers\Api\Tracking\Weight\StoreWeightController;
use App\Http\Controllers\Api\Tracking\Nutrition\StoreNutritionController;
use App\Http\Controllers\Api\Tracking\Nutrition\GetNutritionController;
use App\Http\Controllers\Api\Gamification\GamificationSummaryController;
use App\Http\Controllers\Api\Gamification\PatientAchievementsController;
use App\Http\Controllers\Api\Gamification\AchievementCatalogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]);


Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
    });

    Route::prefix('patients/{patient}')->group(function () {
        Route::post('/avatar', UploadAvatarController::class);
        Route::post('/day', GetPatientDayController::class);

        // Weight
        Route::prefix('weight')->name('weight.')->group(function () {
            Route::post(
                '/',
                StoreWeightController::class
            );
            Route::get('/', GetWeightController::class);
            Route::get('/latest', LatestWeightController::class);
            Route::get('/stats', StatsWeightController::class);
            Route::get('/chart', ChartDataWeightController::class);
        });

        Route::prefix('activities')->name('activities.')->group(function () {
            Route::post('/', StoreActivityController::class);
            Route::get('/', GetActivityController::class);
            Route::get('/day', DayActivityController::class);
            Route::get('/stats', StatsActivityController::class);
            Route::get('/current-week', CurrentWeekActivityController::class);
        });

        // Nutrition
        Route::prefix('nutrition')->name('nutrition.')->group(function () {
            Route::post('/', StoreNutritionController::class);
            Route::get('/', GetNutritionController::class);
        });

        // Gamification
        Route::prefix('gamification')->name('gamification.')->group(function () {
            Route::get('/summary', GamificationSummaryController::class);
            Route::get('/achievements', PatientAchievementsController::class);
        });
    });

    Route::get('/achievements', AchievementCatalogController::class);
});
