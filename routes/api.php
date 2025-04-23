<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:otp');

Route::post('verify-otp',[AuthController::class,'verifyOtp']);
