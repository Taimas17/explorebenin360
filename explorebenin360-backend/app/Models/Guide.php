<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Review;

class Guide extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'languages_json',
        'specialties_json',
        'bio',
        'avatar_url',
        'city',
        'lat',
        'lng',
        'price_per_day',
        'currency',
        'verified',
        'status',
    ];

    protected $hidden = [
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'languages_json' => 'array',
            'specialties_json' => 'array',
            'verified' => 'boolean',
            'rating_avg' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
