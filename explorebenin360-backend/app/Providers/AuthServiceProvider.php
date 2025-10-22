<?php

namespace App\Providers;

use App\Models\Media;
use App\Policies\MediaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Media::class => MediaPolicy::class,
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
