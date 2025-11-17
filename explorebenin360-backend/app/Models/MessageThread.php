<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject', 'user_id', 'recipient_id', 'related_offering_id', 'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function relatedOffering()
    {
        return $this->belongsTo(Offering::class, 'related_offering_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'thread_id')->latestOfMany();
    }

    // Compter messages non lus pour un utilisateur
    public function unreadCountFor(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }

    // Vérifier si l'utilisateur est participant
    public function hasParticipant(int $userId): bool
    {
        return $this->user_id === $userId || $this->recipient_id === $userId;
    }
}
