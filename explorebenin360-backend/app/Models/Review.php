<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'user_id', 'offering_id', 'rating', 'comment',
        'photos_json', 'status', 'moderation_note', 'moderated_by', 'moderated_at', 'published_at'
    ];

    protected $casts = [
        'photos_json' => 'array',
        'rating' => 'integer',
        'moderated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'approved')->whereNotNull('published_at');
    }

    public function approve(?int $moderatorId = null): void
    {
        $this->update([
            'status' => 'approved',
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'published_at' => now(),
        ]);
    }

    public function reject(string $reason, ?int $moderatorId = null): void
    {
        $this->update([
            'status' => 'rejected',
            'moderation_note' => $reason,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
        ]);
    }
}
