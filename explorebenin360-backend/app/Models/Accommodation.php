<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Reviewable;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes, Reviewable;

    protected $fillable = [
        'provider_id',
        'place_id',
        'title',
        'slug',
        'type',
        'description',
        'address',
        'city',
        'lat',
        'lng',
        'price_per_night',
        'currency',
        'amenities_json',
        'capacity',
        'featured',
        'status',
        'cover_image_url',
    ];

    protected $hidden = [
        'provider_id',
    ];

    protected function casts(): array
    {
        return [
            'amenities_json' => 'array',
            'featured' => 'boolean',
            'rating_avg' => 'decimal:2',
            'price_per_night' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
