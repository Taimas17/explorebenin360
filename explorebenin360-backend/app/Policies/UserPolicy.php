<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id || $user->hasRole('admin');
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->id === $targetUser->id || $user->hasRole('admin');
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasRole('admin') && $user->id !== $targetUser->id;
    }

    public function suspend(User $user, User $targetUser): bool
    {
        return $user->hasRole('admin') && $user->id !== $targetUser->id;
    }

    public function manageRoles(User $user, User $targetUser): bool
    {
        return $user->hasRole('admin') && $user->id !== $targetUser->id;
    }
}