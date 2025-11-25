<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'offering_id',
        'start_date',
        'end_date',
        'guests',
        'amount',
        'currency',
    ];

    protected $hidden = [
        'payment_ref',
        'payment_provider',
    ];

    protected $with = ['user', 'offering'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'guests' => 'integer',
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'meta' => 'array',
        'webhook_processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
