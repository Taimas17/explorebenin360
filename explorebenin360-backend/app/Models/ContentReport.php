<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id', 'reportable_type', 'reportable_id', 'reason',
        'description', 'status', 'reviewed_by', 'resolution_note', 'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function resolve(string $note, int $reviewerId): void
    {
        $this->update([
            'status' => 'resolved',
            'resolution_note' => $note,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    public function dismiss(string $note, int $reviewerId): void
    {
        $this->update([
            'status' => 'dismissed',
            'resolution_note' => $note,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }
}
