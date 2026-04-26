<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\PostController;
use App\Http\Controllers\api\v1\PromptGenerationController;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::prefix('v1')->group(function() {
        Route::apiResource('posts', PostController::class);

        Route::apiResource('prompt-generations', PromptGenerationController::class);
    });
});

require __DIR__.'/auth.php';
