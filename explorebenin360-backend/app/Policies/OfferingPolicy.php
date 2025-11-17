<?php

namespace App\Policies;

use App\Models\Offering;
use App\Models\User;

class OfferingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Offering $offering): bool
    {
        if ($offering->status === 'published') return true;
        if ($user && $offering->provider_id === $user->id) return true;
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        return false;
    }

    public function create(User $user): bool
    {
        return method_exists($user, 'isProvider') ? $user->isProvider() : false;
    }

    public function update(User $user, Offering $offering): bool
    {
        if ($offering->provider_id === $user->id) return true;
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        return false;
    }

    public function delete(User $user, Offering $offering): bool
    {
        if ($offering->provider_id === $user->id) return true;
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        return false;
    }
}
