<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccommodationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
            'featured' => (bool) $this->featured,
            'status' => $this->status,
            'cover_image_url' => $this->cover_image_url,
        ];
    }
}
