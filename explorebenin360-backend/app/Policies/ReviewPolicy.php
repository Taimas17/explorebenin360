<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Review $review): bool
    {
        if ($review->status === 'approved') {
            return true;
        }
        if (!$user) {
            return false;
        }
        if ($review->user_id === $user->id) {
            return true;
        }
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        if ($review->user_id !== $user->id) {
            return false;
        }
        if (in_array($review->status, ['approved', 'rejected'])) {
            return false;
        }
        return true;
    }

    public function delete(User $user, Review $review): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        if ($review->user_id === $user->id && $review->status === 'pending') {
            return true;
        }
        return false;
    }

    public function moderate(User $user, Review $review): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }
        return false;
    }
}
