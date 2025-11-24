<?php

namespace App\Policies;

use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Place $place): bool
    {
        if ($place->status === 'published') return true;
        if (!$user) return false;
        return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
    }

    public function create(User $user): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function update(User $user, Place $place): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function delete(User $user, Place $place): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }
}
