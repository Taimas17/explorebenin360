<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'flagged_at' => 'datetime',
    ];

    public function flag(string $reason): void
    {
        $this->update([
            'flagged_at' => now(),
            'flagged_reason' => $reason,
        ]);
    }

    public function unflag(): void
    {
        $this->update([
            'flagged_at' => null,
            'flagged_reason' => null,
        ]);
    }

    public function reports()
    {
        return $this->morphMany(ContentReport::class, 'reportable');
    }
}
