<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    public function saved(Review $review): void
    {
        $review->refresh();
        if (in_array($review->status, ['published','rejected'])) {
            $parent = $review->reviewable;
            if ($parent && method_exists($parent, 'updateAverageRating')) {
                $parent->updateAverageRating();
            }
        }
    }

    public function deleted(Review $review): void
    {
        $parent = $review->reviewable;
        if ($parent && method_exists($parent, 'updateAverageRating')) {
            $parent->updateAverageRating();
        }
    }
}
