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
        $roles = $this->relationLoaded('roles') ? $this->roles->pluck('name') : [];

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,

            // Profile fields from main
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

            // Business/provider fields
            'roles' => $roles,
            'provider_status' => $this->provider_status,
            'business_name' => $this->business_name,
            'bio' => $this->bio,

            // Admin management fields
            'account_status' => $this->account_status,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'last_login_at' => $this->last_login_at,
            'login_count' => $this->login_count,

            // Suspension details (conditional)
            'suspended_at' => $this->when($this->isSuspended() || $this->isBanned(), $this->suspended_at),
            'suspension_reason' => $this->when($this->isSuspended() || $this->isBanned(), $this->suspension_reason),
            'suspended_by' => $this->when($this->suspendedBy, fn () => [
                'id' => $this->suspendedBy->id,
                'name' => $this->suspendedBy->name,
            ]),

            // Counts
            'bookings_count' => $this->whenCounted('bookings'),
            'favorites_count' => $this->whenCounted('favorites'),
            'offerings_count' => $this->whenCounted('offerings'),
        ];

        return $data;
    }
}
