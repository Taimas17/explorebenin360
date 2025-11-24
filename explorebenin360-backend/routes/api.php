<?php

use App\Http\Controllers\Api\AccommodationController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\OfferingController;
use App\Http\Controllers\Api\Payments\PaystackWebhookController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProviderOfferingController;
use App\Http\Controllers\Api\ProviderApplicationController;
use App\Http\Controllers\Api\AdminProviderController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public content
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

    // Auth endpoints (Sanctum token based)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::middleware(['sanctum.cookie', 'auth:sanctum'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // Offerings - public read-only
    Route::get('/offerings', [OfferingController::class, 'index']);
    Route::get('/offerings/{slug}', [OfferingController::class, 'show']);

    // Checkout / Bookings
    Route::middleware(['sanctum.cookie', 'auth:sanctum'])->group(function () {
        Route::post('/checkout/session', [BookingController::class, 'createCheckoutSession']);
        Route::get('/bookings', [BookingController::class, 'myIndex']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    });

    // Favorites
    Route::middleware(['sanctum.cookie', 'auth:sanctum'])->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites', [FavoriteController::class, 'store']);
        Route::post('/favorites/remove', [FavoriteController::class, 'remove']);
    });

    // Provider & Admin JSON endpoints
    Route::middleware(['sanctum.cookie', 'auth:sanctum'])->group(function () {
        Route::get('/provider/bookings', [BookingController::class, 'providerIndex']);
        Route::patch('/provider/bookings/{id}', [BookingController::class, 'providerUpdate']);

        Route::get('/admin/bookings', [BookingController::class, 'adminIndex']);
        Route::patch('/admin/bookings/{id}', [BookingController::class, 'adminUpdate']);

        Route::post('/provider/apply', [ProviderApplicationController::class, 'apply']);
        Route::get('/provider/status', [ProviderApplicationController::class, 'status']);
        Route::get('/provider/offerings', [ProviderOfferingController::class, 'index']);
        Route::post('/provider/offerings', [ProviderOfferingController::class, 'store']);
        Route::get('/provider/offerings/{id}', [ProviderOfferingController::class, 'show']);
        Route::patch('/provider/offerings/{id}', [ProviderOfferingController::class, 'update']);
        Route::delete('/provider/offerings/{id}', [ProviderOfferingController::class, 'destroy']);
        Route::patch('/provider/offerings/{id}/availability', [ProviderOfferingController::class, 'updateAvailability']);
        Route::get('/provider/analytics', [ProviderOfferingController::class, 'analytics']);

        Route::get('/admin/providers', [AdminProviderController::class, 'index']);
        Route::patch('/admin/providers/{id}/approve', [AdminProviderController::class, 'approve']);
        Route::patch('/admin/providers/{id}/reject', [AdminProviderController::class, 'reject']);

        // Protected media mgmt
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
    });

    // Notifications (database channel)
    Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Paystack webhook
    Route::post('/payments/paystack/webhook', [PaystackWebhookController::class, 'handle']);
});
