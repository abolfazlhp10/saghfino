<?php

use App\Http\Controllers\Core\RealEstatesController;
use Illuminate\Support\Facades\Route;

Route::get('/real-estate-agents', [RealEstatesController::class, 'getRealEstateAgents']);

Route::get('/real-estate-agents/{agent_id}', [RealEstatesController::class, 'getRealEstateAgentById']);

Route::prefix('real-estates')->group(function () {

    Route::get('/', [RealEstatesController::class, 'getAllRealEstates']);

    Route::get('/{id}', [RealEstatesController::class, 'getRealEstateById']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/create', [RealEstatesController::class, 'save']);
        Route::post('/rate', [RealEstatesController::class, 'rateRealEsate']);
        Route::post('/update/{realestate}', [RealEstatesController::class, 'updateRealEstateProfile']);
    });

});
