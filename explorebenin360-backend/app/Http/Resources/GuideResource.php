<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'languages' => $this->languages_json ?? [],
            'specialties' => $this->specialties_json ?? [],
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'city' => $this->city,
            'lat' => $this->lat !== null ? (float) $this->lat : null,
            'lng' => $this->lng !== null ? (float) $this->lng : null,
            'price_per_day' => $this->price_per_day !== null ? (float) $this->price_per_day : null,
            'currency' => $this->currency,
            'verified' => (bool) $this->verified,
            'rating_avg' => (float) $this->rating_avg,
            'review_count' => $this->review_count,
            'status' => $this->status,
        ];
    }
}
