<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\UserController;

// Route for user
Route::post('/user', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::get('/user', [UserController::class, 'getUser'])->middleware('auth:sanctum');
Route::get('/user/{id}', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::patch('/user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

// Route for presence
Route::apiResource('presence', PresenceController::class)->middleware('auth:sanctum');
Route::get('/presenceUpdate', [PresenceController::class, 'edit']);
Route::post('/update-presence-status/{id}', [PresenceController::class, 'updatePresenceStatus'])->middleware('auth:sanctum');


Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});