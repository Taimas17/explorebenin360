<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'opening_hours_json' => 'array',
        'tags' => 'array',
        'featured' => 'boolean',
        'rating_avg' => 'decimal:2',
        'price_from' => 'decimal:2',
    ];
}
