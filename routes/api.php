<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\PostController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function() {
    return ["message" => "Hello Laravel API"];
});

Route::prefix('v1')->group(function() {

    Route::apiResource('posts', PostController::class);

});
