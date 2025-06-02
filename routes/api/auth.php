<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:sendOtp');

Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verifyOtp');

Route::post('register', [AuthController::class, 'register']);

Route::post('update-user-profile', [AuthController::class, 'updateUserProfile'])->middleware('auth:sanctum');
