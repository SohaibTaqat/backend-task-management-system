<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->name('tasks.update-status');

    // User Management (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
