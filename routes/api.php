<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsCategoryController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\CommunityController;

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
    Route::prefix('knowledge-bases')->group(function () {

        Route::get('/', [KnowledgeBaseController::class, 'index']);        
        Route::post('/', [KnowledgeBaseController::class, 'store']);       
        Route::get('/{id}', [KnowledgeBaseController::class, 'show']);     
        Route::put('/{id}', [KnowledgeBaseController::class, 'update']);   
        Route::delete('/{id}', [KnowledgeBaseController::class, 'destroy']); 

    });

    

    Route::prefix('communities')->group(function () {

        Route::get('/', [CommunityController::class, 'index']);
        Route::post('/', [CommunityController::class, 'store']);
        Route::get('/{id}', [CommunityController::class, 'show']);
        Route::put('/{id}', [CommunityController::class, 'update']);
        Route::delete('/{id}', [CommunityController::class, 'destroy']);

    });
});