<?php

use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PlaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{id}', [MediaController::class, 'show']);

    Route::get('/places', [PlaceController::class, 'index']);
    Route::get('/places/{slug}', [PlaceController::class, 'show']);

    Route::get('/accommodations', [AccommodationController::class, 'index']);
    Route::get('/accommodations/{slug}', [AccommodationController::class, 'show']);

    Route::get('/guides', [GuideController::class, 'index']);
    Route::get('/guides/{slug}', [GuideController::class, 'show']);

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{slug}', [EventController::class, 'show']);

    Route::middleware(['auth'])->group(function () {
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
    });
});
