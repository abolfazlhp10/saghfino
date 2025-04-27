<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Core\AdController;
use Illuminate\Support\Facades\Route;

Route::post('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp');

Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verifyOtp');

Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('new-advertisement', [AdController::class, 'store']);

    Route::delete('ad/{ad}', [AdController::class,'destroy']);

});
