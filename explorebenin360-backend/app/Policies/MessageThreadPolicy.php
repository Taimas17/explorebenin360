<?php

namespace App\Policies;

use App\Models\MessageThread;
use App\Models\User;

class MessageThreadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MessageThread $thread): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        return $thread->traveler_id === $user->id || $thread->provider_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MessageThread $thread): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        return false;
    }

    public function reply(User $user, MessageThread $thread): bool
    {
        return $thread->traveler_id === $user->id || $thread->provider_id === $user->id;
    }
}