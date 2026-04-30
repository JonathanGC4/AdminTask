<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::put('/profile',          [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);

    Route::apiResource('tasks', TaskController::class);

    // Comentarios anidados bajo tareas
    Route::get   ('tasks/{task}/comments',           [CommentController::class, 'index']);
    Route::post  ('tasks/{task}/comments',           [CommentController::class, 'store']);
    Route::delete('tasks/{task}/comments/{comment}', [CommentController::class, 'destroy']);

    Route::middleware('admin')->group(function () {
        Route::get   ('/users',          [UserController::class, 'index']);
        Route::post  ('/users',          [UserController::class, 'store']);
        Route::delete('/users/{user}',   [UserController::class, 'destroy']);
    });
});
