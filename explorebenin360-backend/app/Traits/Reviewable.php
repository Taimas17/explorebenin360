<?php

namespace App\Traits;

use App\Models\Review;
use Illuminate\Support\Facades\Schema;

trait Reviewable
{
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function publishedReviews()
    {
        return $this->morphMany(Review::class, 'reviewable')->published();
    }

    public function getAverageRatingAttribute()
    {
        return (float) ($this->publishedReviews()->avg('rating') ?? 0);
    }

    public function getTotalReviewsAttribute()
    {
        return (int) $this->publishedReviews()->count();
    }

    public function updateAverageRating(): void
    {
        $avg = (float) ($this->publishedReviews()->avg('rating') ?? 0);
        $count = (int) $this->publishedReviews()->count();
        $updates = [];
        if (Schema::hasColumn($this->getTable(), 'rating_avg')) {
            $updates['rating_avg'] = round($avg, 2);
        }
        if (Schema::hasColumn($this->getTable(), 'review_count')) {
            $updates['review_count'] = $count;
        }
        if (!empty($updates)) {
            $this->update($updates);
        }
    }
}
