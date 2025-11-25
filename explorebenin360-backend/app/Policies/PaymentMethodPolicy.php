<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProvider();
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        return $paymentMethod->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isProvider();
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        if ($paymentMethod->is_verified) {
            return false;
        }
        return $paymentMethod->user_id === $user->id;
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        if ($paymentMethod->user_id === $user->id) {
            return $paymentMethod->payouts()->count() === 0;
        }
        return false;
    }
}
