<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $roles = [];
        if ($this->relationLoaded('roles')) {
            $roles = $this->roles->map(fn($r) => ['name' => is_string($r) ? $r : $r->name])->values();
        }

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'cover_image_url' => $this->cover_image_url,
            'date_of_birth' => optional($this->date_of_birth)?->toDateString(),
            'age' => $this->age,
            'gender' => $this->gender,
            'country' => $this->country,
            'city' => $this->city,
            'full_location' => $this->full_location,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'website_url' => $this->website_url,
            'social_links' => $this->social_links,
            'preferences' => $this->preferences,
            'about_me' => $this->about_me,
            'roles' => $roles,
            'provider_status' => $this->provider_status,
            'business_name' => $this->business_name,
            'bio' => $this->bio,
        ];

        if ($this->whenLoaded('bookings_count')) {
            $data['bookings_count'] = $this->bookings_count;
        }
        if ($this->whenLoaded('favorites_count')) {
            $data['favorites_count'] = $this->favorites_count;
        }
        if ($this->whenLoaded('offerings_count')) {
            $data['offerings_count'] = $this->offerings_count;
        }

        return $data;
    }
}
