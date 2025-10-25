<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', 'logout');
    });
});

Route::middleware('auth:sanctum')->prefix('tasks')->controller(TaskController::class)->group(function () {
    Route::get('/', 'index')->middleware('abilities:task_read');
    Route::post('/', 'store')->middleware('abilities:task_create');
    Route::get('/{task}', 'show')->middleware('abilities:task_read');
    Route::put('/{task}', 'update')->middleware('abilities:task_update');
    Route::delete('/{task}', 'destroy')->middleware('abilities:task_delete');
});
