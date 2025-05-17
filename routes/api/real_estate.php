<?php

use App\Http\Controllers\Core\RealEstatesController;
use Illuminate\Support\Facades\Route;

Route::get('/real-estate-agents', [RealEstatesController::class, 'getRealEstateAgents']);

Route::get('/real-estate-agents/{agent_id}', [RealEstatesController::class, 'getRealEstateAgentById']);
