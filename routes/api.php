<?php

use App\Http\Controllers\API\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route ::get('/test', [TestController::class, 'index']);

Route::apiResource('projects', \App\Http\Controllers\API\ProjectController::class);

Route ::apiResource('tasks', \App\Http\Controllers\API\TaskController::class);