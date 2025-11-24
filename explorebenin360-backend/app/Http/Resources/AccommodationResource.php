<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccommodationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $plain = trim(strip_tags((string) $this->description));
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'price_per_night' => (float) $this->price_per_night,
            'currency' => $this->currency,
            'amenities' => $this->amenities_json ?? [],
            'capacity' => $this->capacity,
            'rating_avg' => (float) $this->rating_avg,
            'review_count' => $this->review_count,
            'rating' => round((float) ($this->rating_avg ?? $this->average_rating), 1),
            'total_reviews' => (int) ($this->total_reviews ?? $this->review_count),
            'reviews_summary' => [
                'average' => round((float) ($this->average_rating ?? $this->rating_avg), 1),
                'total' => (int) ($this->total_reviews ?? $this->review_count),
                '5_star' => $this->publishedReviews()->where('rating', 5)->count(),
                '4_star' => $this->publishedReviews()->where('rating', 4)->count(),
                '3_star' => $this->publishedReviews()->where('rating', 3)->count(),
                '2_star' => $this->publishedReviews()->where('rating', 2)->count(),
                '1_star' => $this->publishedReviews()->where('rating', 1)->count(),
            ],
            'featured' => (bool) $this->featured,
            'status' => $this->status,
            'cover_image_url' => $this->cover_image_url,
            'seo' => [
                'title' => $this->title . ' — ExploreBenin360',
                'description' => mb_substr($plain, 0, 160),
                'image' => $this->cover_image_url,
                'path' => '/hebergements/' . $this->slug,
            ],
        ];
    }
}
