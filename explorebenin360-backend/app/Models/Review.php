<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['is_editable'];

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'user_id',
        'booking_id',
        'rating',
        'title',
        'body',
        'verified_purchase',
        'status',
    ];

    protected $hidden = [
        'moderated_by',
        'moderation_reason',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'verified_purchase' => 'boolean',
        'response_at' => 'datetime',
        'moderated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'response_by');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    public function scopeForReviewable($query, string $type, int $id)
    {
        return $query->where([
            'reviewable_type' => $type,
            'reviewable_id' => $id,
        ]);
    }

    public function getIsEditableAttribute(): bool
    {
        if ($this->moderated_at || $this->moderated_by) return false;
        $created = $this->created_at instanceof Carbon ? $this->created_at : Carbon::parse($this->created_at);
        return $created->diffInHours(now()) < 24;
    }
}
