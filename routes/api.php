<?php

use App\Http\Controllers\API\V1\PositionController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\Auth\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/token', [TokenController::class, 'getToken']);

Route::apiResource('users', UserController::class)->only(['index', 'show'])->withoutMiddleware(['auth.registration_token']);;
Route::apiResource('users', UserController::class)->only(['store'])->middleware('auth.registration_token');
Route::apiResource('positions', PositionController::class)->only(['index']);