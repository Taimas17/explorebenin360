<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'reason' => $this->review->moderation_reason,
            'message' => 'Votre avis a été rejeté',
        ];
    }
}
