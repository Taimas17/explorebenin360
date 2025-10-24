<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
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
            'city' => $this->city,
            'country' => $this->country,
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'price_from' => $this->price_from !== null ? (float) $this->price_from : null,
            'opening_hours' => $this->opening_hours_json,
            'tags' => $this->tags ?? [],
            'cover_image_url' => $this->cover_image_url,
            'rating_avg' => (float) $this->rating_avg,
            'review_count' => $this->review_count,
            'featured' => (bool) $this->featured,
            'status' => $this->status,
            'seo' => [
                'title' => $this->title . ' — ExploreBenin360',
                'description' => mb_substr($plain, 0, 160),
                'image' => $this->cover_image_url,
                'path' => '/destinations/' . $this->slug,
            ],
        ];
    }
}
