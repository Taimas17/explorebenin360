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
}
