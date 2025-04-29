<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Core\AdController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('new-advertisement', [AdController::class, 'store']);

    Route::delete('ad/{ad}', [AdController::class,'destroy']);

    Route::get('ad/show/{ad}',[AdController::class,'show']);

    Route::get('ads',[AdController::class,'index']);

});