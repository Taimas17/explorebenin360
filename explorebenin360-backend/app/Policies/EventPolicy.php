<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Event $event): bool
    {
        if ($event->status === 'published') return true;
        if (!$user) return false;
        return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false;
    }

    public function create(User $user): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function update(User $user, Event $event): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }

    public function delete(User $user, Event $event): bool { return method_exists($user, 'hasRole') ? $user->hasRole('admin') : false; }
}
