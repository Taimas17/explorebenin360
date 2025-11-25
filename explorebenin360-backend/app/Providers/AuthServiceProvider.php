<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\Booking;
use App\Models\Offering;
use App\Models\User;
use App\Models\Accommodation;
use App\Models\Article;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Place;
use App\Models\MessageThread;
use App\Models\Review;
use App\Policies\UserPolicy;
use App\Policies\MediaPolicy;
use App\Policies\BookingPolicy;
use App\Policies\OfferingPolicy;
use App\Policies\AccommodationPolicy;
use App\Policies\ArticlePolicy;
use App\Policies\EventPolicy;
use App\Policies\GuidePolicy;
use App\Policies\PlacePolicy;
use App\Policies\MessageThreadPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Media::class => MediaPolicy::class,
        Booking::class => BookingPolicy::class,
        Offering::class => OfferingPolicy::class,
        User::class => UserPolicy::class,
        Accommodation::class => AccommodationPolicy::class,
        Article::class => ArticlePolicy::class,
        Event::class => EventPolicy::class,
        Guide::class => GuidePolicy::class,
        Place::class => PlacePolicy::class,
        MessageThread::class => MessageThreadPolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage-media', function ($user) {
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('admin') || $user->hasRole('content manager') || $user->hasRole('content-manager');
            }
            if (property_exists($user, 'role')) {
                return in_array(strtolower($user->role), ['admin', 'content manager', 'content-manager']);
            }
            return false;
        });
    }
}
