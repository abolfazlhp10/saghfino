<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/dashboard')->middleware(['auth:sanctum','isAdmin'])->group(function () {
    
    Route::get('/ads-users-counts', [AdminController::class, 'getCounts']);

    Route::get('users',[AdminController::class,'getAllUsers']);

    Route::get('agents',[AdminController::class,'getAllAgents']);

    Route::get('real-estates',[AdminController::class,'getRealEstates']);

    Route::get('ads',[AdminController::class,'getAllAds']);

    Route::delete('ads/{ad_id}',[AdminController::class,'deleteAd']);
    
    Route::post('ads/change-status/{ad}',[AdminController::class,'changeAdStatus']);
});
