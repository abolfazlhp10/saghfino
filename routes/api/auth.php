<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:sendOtp');

Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verifyOtp');

Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('update-user-profile', [AuthController::class, 'updateUserProfile']);
    Route::post('update-realestate-profile/{realestate}', [AuthController::class, 'updateRealEstateProfile']);
});
