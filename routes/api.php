<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',[\App\Http\Controllers\api\AuthController::class,'login'])->name('login');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\api\AuthController::class, 'logout']);
    Route::get('/tasks', [\App\Http\Controllers\api\TaskController::class, 'index']);
    Route::get('/tasks/{id}', [\App\Http\Controllers\api\TaskController::class, 'show']);
    Route::put('/tasks/{id}', [\App\Http\Controllers\api\TaskController::class, 'update']);
    Route::post('/tasks', [\App\Http\Controllers\api\TaskController::class, 'store']);
    Route::delete('/tasks/{id}', [\App\Http\Controllers\api\TaskController::class, 'delete']);
});
