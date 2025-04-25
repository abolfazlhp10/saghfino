<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp');

Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:verifyOtp');

Route::post('register', [AuthController::class, 'register']);
