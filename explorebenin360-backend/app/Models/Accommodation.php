<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'amenities_json' => 'array',
        'featured' => 'boolean',
        'rating_avg' => 'decimal:2',
        'price_per_night' => 'decimal:2',
    ];
}
