<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\HealthCheckController;
use App\Http\Controllers\API\V1\PostController;

Route::prefix('v1')->group(function () {
    Route::get('health', [HealthCheckController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('posts', PostController::class);
    });
});
