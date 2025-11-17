<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'offering_id', 'start_date', 'end_date', 'guests',
        'status', 'amount', 'currency', 'commission_amount', 'payment_provider',
        'payment_ref', 'payment_status', 'cancel_reason', 'meta'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'guests' => 'integer',
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'meta' => 'array',
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

    public function canBeReviewed(): bool
    {
        return $this->status === 'confirmed' && is_null($this->review);
    }
}
