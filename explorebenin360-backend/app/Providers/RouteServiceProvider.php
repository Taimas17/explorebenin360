<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Rate limiter pour authentification
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again later.'
                    ], 429, $headers);
                });
        });
        
        // Rate limiter pour API générale
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
        
        // Rate limiter pour webhooks
        RateLimiter::for('webhook', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });
        
        // Rate limiter pour uploads
        RateLimiter::for('upload', function (Request $request) {
            return Limit::perHour(20)->by($request->user()?->id ?: $request->ip());
        });
        
        // Rate limiter pour favoris
        RateLimiter::for('favorites', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()->id);
        });
        
        // Rate limiter pour notifications
        RateLimiter::for('notifications', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()->id);
        });
        
        // Rate limiter strict pour admin actions
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()->id);
        });
    }
}