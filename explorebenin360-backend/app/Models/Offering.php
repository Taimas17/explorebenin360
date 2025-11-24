<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Reviewable;

class Offering extends Model
{
    use HasFactory, SoftDeletes, Reviewable;

    protected $fillable = [
        'place_id',
        'type',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'capacity',
        'availability_json',
        'status',
        'cover_image_url',
        'gallery_json',
        'cancellation_policy',
    ];

    protected $hidden = [
        'provider_id',
    ];

    protected $with = ['place'];

    protected $casts = [
        'availability_json' => 'array',
        'gallery_json' => 'array',
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
}
