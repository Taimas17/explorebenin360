<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'booking_id',
        'offering_id',
        'traveler_id',
        'provider_id',
        'status',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function traveler()
    {
        return $this->belongsTo(User::class, 'traveler_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function offering()
    {
        return $this->belongsTo(Offering::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    public function getUnreadCountForUser($userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function getLastMessagePreview(): string
    {
        $lastMessage = $this->messages()->latest('created_at')->first();
        if (!$lastMessage) {
            return '';
        }
        return \Illuminate\Support\Str::limit($lastMessage->body, 100, '...');
    }
}