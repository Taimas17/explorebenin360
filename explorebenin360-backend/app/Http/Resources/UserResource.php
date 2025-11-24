<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'business_name' => $this->business_name,
            'bio' => $this->bio,
            'roles' => $this->roles->pluck('name'),
            'provider_status' => $this->provider_status,
            'account_status' => $this->account_status,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'last_login_at' => $this->last_login_at,
            'login_count' => $this->login_count,
            'suspended_at' => $this->when($this->isSuspended() || $this->isBanned(), $this->suspended_at),
            'suspension_reason' => $this->when($this->isSuspended() || $this->isBanned(), $this->suspension_reason),
            'suspended_by' => $this->when($this->suspendedBy, fn () => [
                'id' => $this->suspendedBy->id,
                'name' => $this->suspendedBy->name,
            ]),
            'bookings_count' => $this->whenCounted('bookings'),
            'favorites_count' => $this->whenCounted('favorites'),
            'offerings_count' => $this->whenCounted('offerings'),
        ];
    }
}