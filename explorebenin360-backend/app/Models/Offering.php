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
        'price', 'currency', 'capacity', 'availability_json', 'status',
        'cover_image_url', 'gallery_json', 'cancellation_policy',
        'flagged_at', 'flagged_reason'
    ];

    protected $casts = [
        'availability_json' => 'array',
        'gallery_json' => 'array',
        'price' => 'decimal:2',
        'capacity' => 'integer',
        'flagged_at' => 'datetime',
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

    public function flag(string $reason): void
    {
        $this->update([
            'flagged_at' => now(),
            'flagged_reason' => $reason,
        ]);
    }

    public function unflag(): void
    {
        $this->update([
            'flagged_at' => null,
            'flagged_reason' => null,
        ]);
    }

    public function reports()
    {
        return $this->morphMany(ContentReport::class, 'reportable');
    }
}

