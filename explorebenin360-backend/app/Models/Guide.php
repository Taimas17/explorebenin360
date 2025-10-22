<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guide extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'languages_json' => 'array',
        'specialties_json' => 'array',
        'verified' => 'boolean',
        'rating_avg' => 'decimal:2',
        'price_per_day' => 'decimal:2',
    ];
}
