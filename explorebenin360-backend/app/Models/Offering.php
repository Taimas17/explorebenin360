<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'provider_id', 'place_id', 'type', 'title', 'slug', 'description',
        'price', 'currency', 'capacity', 'availability_json', 'status'
    ];

    protected $casts = [
        'availability_json' => 'array',
        'price' => 'decimal:2',
        'capacity' => 'integer',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->approved();
    }

    public function averageRating(): float
    {
        return (float) ($this->approvedReviews()->avg('rating') ?? 0);
    }

    public function reviewsCount(): int
    {
        return (int) $this->approvedReviews()->count();
    }
}
