<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Review;

class Place extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'description',
        'city',
        'country',
        'lat',
        'lng',
        'price_from',
        'opening_hours_json',
        'tags',
        'cover_image_url',
        'featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours_json' => 'array',
            'tags' => 'array',
            'featured' => 'boolean',
            'rating_avg' => 'decimal:2',
            'price_from' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function approvedReviews()
    {
        return $this->morphMany(Review::class, 'reviewable')->where('status', 'approved');
    }
}
