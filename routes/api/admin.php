<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->middleware(['auth:sanctum','isAdmin'])->group(function () {
    
    Route::get('dashboard/ads-users-counts', [AdminController::class, 'getCounts']);
    
});
