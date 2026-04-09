<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HistoryController;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public parameter routes
Route::get('/parameters', [ParameterController::class, 'getAllParameters']);
Route::get('/parameters/{id}', [ParameterController::class, 'getParameter']);

// Protected routes (require auth token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // Parameters (save/get for authenticated users)
    Route::post('/save-parameters', [ParameterController::class, 'save']);
    Route::get('/get-parameters', [ParameterController::class, 'get']);

    // Generation history
    Route::get('/histories', [HistoryController::class, 'index']);
    Route::post('/histories', [HistoryController::class, 'store']);
    Route::get('/histories/{id}', [HistoryController::class, 'show']);
    Route::delete('/histories/{id}', [HistoryController::class, 'destroy']);
});