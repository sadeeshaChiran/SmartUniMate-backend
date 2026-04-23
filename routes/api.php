<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\NewsCategoryController;



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

});