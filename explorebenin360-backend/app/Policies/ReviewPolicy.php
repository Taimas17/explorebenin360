<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(?User $user): bool { return true; }

    public function view(?User $user, Review $review): bool {
        if ($review->status === 'published') return true;
        if (!$user) return false;
        if ($review->user_id === $user->id) return true;
        if ($user->hasRole('admin')) return true;
        if ($review->reviewable_type === 'App\\Models\\Offering') {
            return optional($review->reviewable)->provider_id === $user->id;
        }
        return false;
    }

    public function create(User $user): bool { return (bool) $user; }

    public function update(User $user, Review $review): bool {
        return $review->user_id === $user->id && $review->is_editable;
    }

    public function delete(User $user, Review $review): bool {
        return $review->user_id === $user->id || $user->hasRole('admin');
    }

    public function moderate(User $user, Review $review): bool {
        return $user->hasRole('admin');
    }

    public function respond(User $user, Review $review): bool {
        if ($user->hasRole('admin')) return true;
        if ($review->reviewable_type === 'App\\Models\\Offering') {
            return optional($review->reviewable)->provider_id === $user->id;
        }
        return false;
    }
}
