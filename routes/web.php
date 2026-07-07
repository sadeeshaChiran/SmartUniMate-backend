<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

// Language locale setting route
Route::get('/set-locale/{locale}', function ($locale) {
    $supported = ['en', 'ta', 'si'];
    if (in_array($locale, $supported)) {
        Cookie::queue('locale', $locale, 60 * 24 * 30); // 30 days
    }
    return redirect()->back();
});
