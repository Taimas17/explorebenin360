<?php

namespace App\Policies;

use App\Models\Guide;
use App\Models\User;

class GuidePolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Guide $guide): bool
    {
        if ($guide->status === 'published') return true;
        if (!$user) return false;
        return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
    }

    public function create(User $user): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function update(User $user, Guide $guide): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function delete(User $user, Guide $guide): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }
}
