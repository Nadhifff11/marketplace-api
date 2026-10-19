<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

// Serve file dari storage/app/public lewat URL /storage/...
// Ini solusi untuk Windows + php artisan serve yang tidak baca junction
Route::get('/storage/{path}', function ($path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return Storage::disk('public')->response($path);
})->where('path', '.*');
