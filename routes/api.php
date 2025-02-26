<?php

use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\SpaceController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Standard CRUD routes
    Route::apiResource('users', UserController::class);

    // Additional email-based routes
    Route::put('users/email/{email}', [UserController::class, 'updateByEmail']);
    Route::delete('users/email/{email}', [UserController::class, 'destroyByEmail']);
    Route::get('users/email/{email}', [UserController::class, 'showByEmail']);
    Route::post('spaces', [SpaceController::class, 'store']);
});

Route::apiResource('spaces', SpaceController::class);

Route::get('/spaces/{id}', [SpaceController::class, 'show']);

Route::get('/spaces/reg/{regNumber}', [SpaceController::class, 'showByRegNumber']);

Route::get('/space-types', [SpaceController::class, 'getSpaceTypes']);

Route::get('/modalities', [SpaceController::class, 'getModalities']);

Route::get('/services', [SpaceController::class, 'getServices']);

Route::get('/islands', [SpaceController::class, 'getIslands']);

Route::get('/search', [SpaceController::class, 'search']);

Route::post('/comments', [CommentController::class, 'store'])->middleware('auth:sanctum');

Route::get('/comments/user', [CommentController::class, 'userComments'])->middleware('auth:sanctum');

Route::post('/reset-password', [UserController::class, 'resetPassword']);
