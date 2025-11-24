<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'offering' => [
                'id' => $this->offering->id,
                'title' => $this->offering->title,
                'slug' => $this->offering->slug,
                'price' => $this->offering->price,
                'currency' => $this->offering->currency,
                'cover_image_url' => $this->offering->cover_image_url,
            ],
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'guests' => $this->guests,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'created_at' => $this->created_at,
        ];
    }
}
