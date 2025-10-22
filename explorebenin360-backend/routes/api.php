<?php

use App\Http\Controllers\Api\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{id}', [MediaController::class, 'show']);

    Route::middleware(['auth'])->group(function () {
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
    });
});
