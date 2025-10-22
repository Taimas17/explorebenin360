<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Media $media): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') || $user->hasRole('content manager') || $user->hasRole('content-manager');
        }
        if (property_exists($user, 'role')) {
            return in_array(strtolower($user->role), ['admin', 'content manager', 'content-manager']);
        }
        return false;
    }

    public function delete(User $user, Media $media): bool
    {
        return $this->create($user);
    }
}
