<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\AcademicModuleController;

Route::prefix('v1')->group(function () {

    // Public News Category Routes
    Route::get('news-categories', [NewsCategoryController::class, 'index']);
    Route::get('news-categories/{news_category}', [NewsCategoryController::class, 'show']);

    // Public News Routes
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::get('category/{id}', [NewsController::class, 'byCategory']);
        Route::get('search/{query}', [NewsController::class, 'search']);
        Route::get('{id}', [NewsController::class, 'show']);
    });

    // Public Knowledge Base Routes
    Route::prefix('knowledge-bases')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index']);        
        Route::get('/{id}', [KnowledgeBaseController::class, 'show']);     
    });

    // Public Communities
    Route::prefix('communities')->group(function () {
        Route::get('/', [CommunityController::class, 'index']);
        Route::get('/{id}', [CommunityController::class, 'show']);
    });

    // Public Academic Module Routes
    Route::get('academic-modules', [AcademicModuleController::class, 'index']);
    Route::get('academic-modules/{id}', [AcademicModuleController::class, 'show']);



    // ── Public ────────────────────────────────────────────────────────────────
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    // ── Admin Auth ───────────────────────────────────────────────────────────
    Route::post('/admin/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'ability:role:admin'])->group(function () {
        Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
        
        // Admin Content Management
        Route::post('news-categories', [NewsCategoryController::class, 'store']);
        Route::put('news-categories/{news_category}', [NewsCategoryController::class, 'update']);
        Route::delete('news-categories/{news_category}', [NewsCategoryController::class, 'destroy']);

        Route::post('news', [NewsController::class, 'store']);
        Route::put('news/{id}', [NewsController::class, 'update']);
        Route::delete('news/{id}', [NewsController::class, 'destroy']);

        Route::post('knowledge-bases', [KnowledgeBaseController::class, 'store']);
        Route::put('knowledge-bases/{id}', [KnowledgeBaseController::class, 'update']);
        Route::delete('knowledge-bases/{id}', [KnowledgeBaseController::class, 'destroy']);

        // Admin Moderation - delete community posts
        Route::delete('admin/communities/{id}', [CommunityController::class, 'destroy']);

        // Admin Academic Modules
        Route::post('academic-modules', [AcademicModuleController::class, 'store']);
        Route::put('academic-modules/{id}', [AcademicModuleController::class, 'update']);
        Route::delete('academic-modules/{id}', [AcademicModuleController::class, 'destroy']);
    });

    // ── Shared routes (accessible to both student and admin roles) ────────────────
    Route::middleware(['auth:sanctum', 'ability:role:student,role:admin'])->group(function () {
        Route::get('/profile',              [ProfileController::class, 'show']);
        Route::put('/profile',              [ProfileController::class, 'update']);
        Route::put('/profile/password',     [ProfileController::class, 'changePassword']);
    });

    // ── Protected (requires Bearer token with student role) ──────────────────
    Route::middleware(['auth:sanctum', 'ability:role:student'])->group(function () {

        // Community Posts
        Route::post('/communities',         [CommunityController::class, 'store']);
        Route::put('/communities/{id}',     [CommunityController::class, 'update']);
        Route::delete('/communities/{id}',  [CommunityController::class, 'destroy']);

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);

        // Student Profile
        Route::post('/profile/avatar',      [ProfileController::class, 'uploadAvatar']);

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
