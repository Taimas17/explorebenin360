<?php

namespace App\Policies;

use App\Models\Payout;
use App\Models\User;

class PayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProvider() || (method_exists($user, 'hasRole') && $user->hasRole('admin'));
    }

    public function view(User $user, Payout $payout): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        return $payout->provider_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isProvider();
    }

    public function cancel(User $user, Payout $payout): bool
    {
        if ($payout->provider_id === $user->id && $payout->status === 'pending') {
            return true;
        }
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return in_array($payout->status, ['pending', 'processing']);
        }
        return false;
    }

    public function process(User $user, Payout $payout): bool
    {
        if (!method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            return false;
        }
        return $payout->status === 'pending';
    }
}
