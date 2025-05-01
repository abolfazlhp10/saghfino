<?php

use App\Http\Controllers\Core\AdController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('new-advertisement', [AdController::class, 'store']);

    Route::delete('ad/{ad}', [AdController::class, 'destroy']);

    Route::get('ad/show/{ad}', [AdController::class, 'show']);

    Route::get('ads', [AdController::class, 'index']);

    Route::post('ads/{ad}/save',[AdController::class,'saveAds']);

    Route::delete('ads/{ad}/unsave',[AdController::class,'unsaveAd']);

    Route::get('ads/saved-ads',[AdController::class,'showSavedAds']);

    Route::delete('ads/saved-ads',[AdController::class,'deleteAllSavedAds']);

    Route::get('my-ads',[AdController::class,'showMyAds']);

});
