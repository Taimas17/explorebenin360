<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
        ];
    }
}
