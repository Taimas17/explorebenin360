<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $plain = trim(strip_tags((string) $this->description));
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'place_id' => $this->place_id,
            'city' => $this->city,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'organizer_name' => $this->organizer_name,
            'organizer_contact' => $this->organizer_contact,
            'description' => $this->description,
            'price' => $this->price !== null ? (float) $this->price : null,
            'currency' => $this->currency,
            'category' => $this->category,
            'cover_image_url' => $this->cover_image_url,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'rating' => round((float) ($this->average_rating ?? 0), 1),
            'total_reviews' => (int) ($this->total_reviews ?? 0),
            'reviews_summary' => [
                'average' => round((float) ($this->average_rating ?? 0), 1),
                'total' => (int) ($this->total_reviews ?? 0),
                '5_star' => $this->publishedReviews()->where('rating', 5)->count(),
                '4_star' => $this->publishedReviews()->where('rating', 4)->count(),
                '3_star' => $this->publishedReviews()->where('rating', 3)->count(),
                '2_star' => $this->publishedReviews()->where('rating', 2)->count(),
                '1_star' => $this->publishedReviews()->where('rating', 1)->count(),
            ],
            'seo' => [
                'title' => $this->title . ' — ExploreBenin360',
                'description' => mb_substr($plain, 0, 160),
                'image' => $this->cover_image_url,
                'path' => '/agenda/' . $this->slug,
            ],
        ];
    }
}
