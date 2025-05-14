<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;


// Route::post('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp');
Route::post('send-otp', [AuthController::class, 'sendOtp']);

// Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verifyOtp');
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);

Route::post('register', [AuthController::class, 'register']);
