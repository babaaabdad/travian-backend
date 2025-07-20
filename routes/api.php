<?php

use App\Http\Controllers\Api\V1\VillageController;
use Illuminate\Support\Facades\Route;

// All routes here are automatically prefixed with '/api' by Laravel.
// The starter template further prefixes them with '/v1'.

Route::middleware('auth:sanctum')->group(function () {
    // GET /api/v1/village
    Route::get('/village', [VillageController::class, 'show']);

    // POST /api/v1/village/upgrade/{building}
    Route::post('/village/upgrade/{building}', [VillageController::class, 'upgrade']);
});