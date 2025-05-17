<?php

use App\Http\Controllers\Core\AdController;
use Illuminate\Support\Facades\Route;


Route::get('ads', [AdController::class, 'index']);

Route::get('ads/filter', [AdController::class, 'adsFilter']);

Route::get('ad/show/{ad}', [AdController::class, 'show']);

Route::get('cities',[AdController::class,'getCities']);

Route::get('/real-estate-agents', [AdController::class,'getRealEstateAgents']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('new-advertisement', [AdController::class, 'store']);

    Route::delete('ad/{ad}', [AdController::class, 'destroy']);

    Route::post('ads/{ad}/save',[AdController::class,'saveAd']);

    Route::delete('ads/{ad}/unsave',[AdController::class,'unsaveAd']);

    Route::get('ads/saved-ads',[AdController::class,'showSavedAds']);

    Route::delete('ads/saved-ads',[AdController::class,'deleteAllSavedAds']);

    Route::get('my-ads',[AdController::class,'showMyAds']);

    Route::delete('my-ads',[AdController::class,'deleteAllMyAds']);

    Route::delete('my-ads/{ad}',[AdController::class,'deleteMyAdsById']);

    
});

