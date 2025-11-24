<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Reviewable;

class Event extends Model
{
    use HasFactory, SoftDeletes, Reviewable;

    protected $fillable = [
        'place_id',
        'title',
        'slug',
        'city',
        'start_date',
        'end_date',
        'organizer_name',
        'organizer_contact',
        'description',
        'price',
        'currency',
        'category',
        'cover_image_url',
        'status',
        'featured',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'price' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
