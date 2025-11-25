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
use App\Http\Controllers\Api\Admin\AccommodationAdminController;
use App\Http\Controllers\Api\Admin\ArticleAdminController;
use App\Http\Controllers\Api\Admin\EventAdminController;
use App\Http\Controllers\Api\Admin\GuideAdminController;
use App\Http\Controllers\Api\Admin\PlaceAdminController;
use App\Http\Controllers\Api\Admin\AdminAnalyticsController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProviderOfferingController;
use App\Http\Controllers\Api\ProviderApplicationController;
use App\Http\Controllers\Api\AdminProviderController;
use App\Http\Controllers\Api\UserAdminController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Routes publiques avec rate limiting par défaut (60/min)
    Route::middleware('throttle:api')->group(function () {
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
        
        Route::get('/offerings', [OfferingController::class, 'index']);
        Route::get('/offerings/{slug}', [OfferingController::class, 'show']);

        Route::get('/reviews', [ReviewController::class, 'index']);
    });

    // Auth endpoints - rate limiting strict (5/min)
    Route::prefix('auth')->middleware('throttle:auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // Checkout / Bookings - rate limiting standard
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active', 'throttle:api'])->group(function () {
        Route::post('/checkout/session', [BookingController::class, 'createCheckoutSession']);
        Route::get('/bookings', [BookingController::class, 'myIndex']);
        Route::get('/bookings/{id}', [BookingController::class, 'show']);
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    });

    // Messages - rate limiting standard (60/min)
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active', 'throttle:api'])->prefix('messages')->group(function () {
        Route::get('/threads', [MessageController::class, 'listThreads']);
        Route::post('/threads', [MessageController::class, 'store']);
        Route::get('/threads/{id}', [MessageController::class, 'show']);
        Route::post('/threads/{id}', [MessageController::class, 'reply']);
        Route::patch('/threads/{id}/close', [MessageController::class, 'close'])->middleware('admin');
    });

    // Favorites - rate limiting spécifique (30/min)
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active', 'throttle:favorites'])->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites', [FavoriteController::class, 'store']);
        Route::post('/favorites/remove', [FavoriteController::class, 'remove']);
    });

    // User profile management
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'throttle:api'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar']);
        Route::post('/profile/cover', [ProfileController::class, 'uploadCover']);
        Route::delete('/profile/cover', [ProfileController::class, 'deleteCover']);
        Route::delete('/profile', [ProfileController::class, 'deleteAccount']);
    });

    // Provider & Admin - rate limiting API standard
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active', 'throttle:api'])->group(function () {
        Route::get('/reviews/my', [ReviewController::class, 'myReviews']);
        Route::get('/reviews/can-review/{bookingId}', [ReviewController::class, 'canReview']);
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews/{id}', [ReviewController::class, 'show']);
        Route::patch('/reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

        Route::get('/provider/bookings', [BookingController::class, 'providerIndex']);
        Route::patch('/provider/bookings/{id}', [BookingController::class, 'providerUpdate']);

        Route::post('/provider/apply', [ProviderApplicationController::class, 'apply']);
        Route::get('/provider/status', [ProviderApplicationController::class, 'status']);
        
        Route::get('/provider/offerings', [ProviderOfferingController::class, 'index']);
        Route::post('/provider/offerings', [ProviderOfferingController::class, 'store']);
        Route::get('/provider/offerings/{id}', [ProviderOfferingController::class, 'show']);
        Route::patch('/provider/offerings/{id}', [ProviderOfferingController::class, 'update']);
        Route::delete('/provider/offerings/{id}', [ProviderOfferingController::class, 'destroy']);
        Route::patch('/provider/offerings/{id}/availability', [ProviderOfferingController::class, 'updateAvailability']);
        Route::get('/provider/analytics', [ProviderOfferingController::class, 'analytics']);

        // Admin endpoints - avec middleware admin + rate limiting
        Route::middleware(['admin', 'throttle:admin'])->group(function () {
            Route::get('/admin/bookings', [BookingController::class, 'adminIndex']);
            Route::patch('/admin/bookings/{id}', [BookingController::class, 'adminUpdate']);
            
            Route::get('/admin/providers', [AdminProviderController::class, 'index']);
            Route::patch('/admin/providers/{id}/approve', [AdminProviderController::class, 'approve']);
            Route::patch('/admin/providers/{id}/reject', [AdminProviderController::class, 'reject']);

            Route::get('/admin/users', [UserAdminController::class, 'index']);
            Route::get('/admin/users/{id}', [UserAdminController::class, 'show']);
            Route::patch('/admin/users/{id}', [UserAdminController::class, 'update']);
            Route::delete('/admin/users/{id}', [UserAdminController::class, 'destroy']);

            Route::post('/admin/users/{id}/suspend', [UserAdminController::class, 'suspend']);
            Route::post('/admin/users/{id}/unsuspend', [UserAdminController::class, 'unsuspend']);
            Route::post('/admin/users/{id}/ban', [UserAdminController::class, 'ban']);

            Route::patch('/admin/users/{id}/roles', [UserAdminController::class, 'updateRoles']);
            Route::post('/admin/users/{id}/reset-password', [UserAdminController::class, 'resetPassword']);

            // Admin content management
            Route::get('/admin/accommodations', [AccommodationAdminController::class, 'index']);
            Route::post('/admin/accommodations', [AccommodationAdminController::class, 'store']);
            Route::get('/admin/accommodations/{id}', [AccommodationAdminController::class, 'show']);
            Route::patch('/admin/accommodations/{id}', [AccommodationAdminController::class, 'update']);
            Route::delete('/admin/accommodations/{id}', [AccommodationAdminController::class, 'destroy']);

            Route::get('/admin/articles', [ArticleAdminController::class, 'index']);
            Route::post('/admin/articles', [ArticleAdminController::class, 'store']);
            Route::get('/admin/articles/{id}', [ArticleAdminController::class, 'show']);
            Route::patch('/admin/articles/{id}', [ArticleAdminController::class, 'update']);
            Route::delete('/admin/articles/{id}', [ArticleAdminController::class, 'destroy']);

            Route::get('/admin/events', [EventAdminController::class, 'index']);
            Route::post('/admin/events', [EventAdminController::class, 'store']);
            Route::get('/admin/events/{id}', [EventAdminController::class, 'show']);
            Route::patch('/admin/events/{id}', [EventAdminController::class, 'update']);
            Route::delete('/admin/events/{id}', [EventAdminController::class, 'destroy']);

            Route::get('/admin/guides', [GuideAdminController::class, 'index']);
            Route::post('/admin/guides', [GuideAdminController::class, 'store']);
            Route::get('/admin/guides/{id}', [GuideAdminController::class, 'show']);
            Route::patch('/admin/guides/{id}', [GuideAdminController::class, 'update']);
            Route::delete('/admin/guides/{id}', [GuideAdminController::class, 'destroy']);

            Route::get('/admin/places', [PlaceAdminController::class, 'index']);
            Route::post('/admin/places', [PlaceAdminController::class, 'store']);
            Route::get('/admin/places/{id}', [PlaceAdminController::class, 'show']);
            Route::patch('/admin/places/{id}', [PlaceAdminController::class, 'update']);
            Route::delete('/admin/places/{id}', [PlaceAdminController::class, 'destroy']);

            // Admin analytics
            Route::prefix('admin/analytics')->group(function () {
                Route::get('/overview', [AdminAnalyticsController::class, 'overview']);
                Route::get('/timeseries', [AdminAnalyticsController::class, 'timeseries']);
                Route::get('/top-content', [AdminAnalyticsController::class, 'topContent']);
                Route::get('/recent-activity', [AdminAnalyticsController::class, 'recentActivity']);
                Route::get('/export', [AdminAnalyticsController::class, 'export']);
            });

            Route::prefix('admin/reviews')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'index']);
                Route::patch('/{id}/approve', [\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'approve']);
                Route::patch('/{id}/reject', [\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'reject']);
                Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'destroy']);
            });
        });
    });
    
    // Media upload - rate limiting upload (20/hour)
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active', 'throttle:upload'])->group(function () {
        Route::post('/media', [MediaController::class, 'store']);
        Route::delete('/media/{id}', [MediaController::class, 'destroy']);
    });

    // Notifications - rate limiting notifications (10/min)
    Route::middleware(['sanctum.cookie', 'auth:sanctum', 'account.active', 'throttle:notifications'])->prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Paystack webhook - rate limiting webhook (100/min)
    Route::middleware('throttle:webhook')
        ->post('/payments/paystack/webhook', [PaystackWebhookController::class, 'handle']);
});
