<?php

namespace App\Observers;

use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewObserver
{
    public function created(Review $review): void
    {
        if ($review->status === 'approved') {
            $this->updateAverages($review);
        }
    }

    public function updated(Review $review): void
    {
        if ($review->isDirty('status')) {
            $this->updateAverages($review);
        }
        if ($review->isDirty('rating') && $review->status === 'approved') {
            $this->updateAverages($review);
        }
    }

    public function deleted(Review $review): void
    {
        $this->updateAverages($review);
    }

    private function updateAverages(Review $review): void
    {
        $reviewableType = $review->reviewable_type;
        $reviewableId = $review->reviewable_id;

        $stats = Review::where('reviewable_type', $reviewableType)
            ->where('reviewable_id', $reviewableId)
            ->where('status', 'approved')
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as count')
            ->first();

        $avgRating = $stats->avg_rating ? round($stats->avg_rating, 2) : 0;
        $count = $stats->count ?? 0;

        $table = match($reviewableType) {
            'App\\Models\\Accommodation' => 'accommodations',
            'App\\Models\\Guide' => 'guides',
            'App\\Models\\Place' => 'places',
            default => null,
        };

        if ($table) {
            DB::table($table)
                ->where('id', $reviewableId)
                ->update([
                    'rating_avg' => $avgRating,
                    'review_count' => $count,
                    'updated_at' => now(),
                ]);
        }
    }
}
