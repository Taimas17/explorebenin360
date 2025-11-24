<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\User;

class AccommodationPolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Accommodation $accommodation): bool
    {
        if ($accommodation->status === 'published') return true;
        if (!$user) return false;
        return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
    }

    public function create(User $user): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function update(User $user, Accommodation $accommodation): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function delete(User $user, Accommodation $accommodation): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }
}
