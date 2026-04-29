<?php

use App\Http\Controllers\API\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route ::get('/test', [TestController::class, 'index'])->middleware('auth:sanctum');

Route::apiResource('projects', \App\Http\Controllers\API\ProjectController::class)->middleware('auth:sanctum');

Route ::apiResource('tasks', \App\Http\Controllers\API\TaskController::class);

// registration api
Route::post('/register', [\App\Http\Controllers\API\AuthController::class, 'register']); //registration api
Route::post('/login', [\App\Http\Controllers\API\AuthController::class, 'login']); //login api
Route::post('/logout', [\App\Http\Controllers\API\AuthController::class, 'logout'])->middleware('auth:sanctum'); //logout api