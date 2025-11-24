<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'model_type',
        'model_id',
        'type',
        'url',
        'provider',
        'alt',
        'caption',
        'width',
        'height',
        'size_bytes',
        'mime',
        'metadata_json',
        'created_by',
    ];

    protected $hidden = [
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'metadata_json' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mediable()
    {
        return $this->morphTo('model');
    }
}
