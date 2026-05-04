<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TimetableController;

Route::prefix('v1')->group(function () {

    // News Category Routes
    Route::apiResource('news-categories', NewsCategoryController::class);

    // News Routes
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::post('/', [NewsController::class, 'store']);
        Route::get('{id}', [NewsController::class, 'show']);
        Route::put('{id}', [NewsController::class, 'update']);
        Route::delete('{id}', [NewsController::class, 'destroy']);
        Route::get('category/{id}', [NewsController::class, 'byCategory']);
        Route::get('search/{query}', [NewsController::class, 'search']);
    });



    // ── Public ────────────────────────────────────────────────────────────────
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // ── Protected (requires Bearer token) ────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);

        // Student Profile
        Route::get('/profile',              [ProfileController::class, 'show']);
        Route::put('/profile',              [ProfileController::class, 'update']);
        Route::post('/profile/avatar',      [ProfileController::class, 'uploadAvatar']);
        Route::put('/profile/password',     [ProfileController::class, 'changePassword']);

        // Timetable
        Route::get('/timetable',            [TimetableController::class, 'index']);
        Route::get('/timetable/{day}',      [TimetableController::class, 'byDay']);
        Route::post('/timetable',           [TimetableController::class, 'store']);
        Route::put('/timetable/{id}',       [TimetableController::class, 'update']);
        Route::delete('/timetable/{id}',    [TimetableController::class, 'destroy']);

        // AI Chat
        Route::post('/chat',                [ChatController::class, 'send']);
        Route::get('/chat/history',         [ChatController::class, 'history']);
        Route::delete('/chat/history',      [ChatController::class, 'clearHistory']);
    });
});
