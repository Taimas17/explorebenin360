<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewResponseNotification extends Notification
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
        $preview = mb_substr((string) $this->review->response, 0, 120);
        return [
            'review_id' => $this->review->id,
            'responder_name' => optional($this->review->responder)->name,
            'response_preview' => $preview,
            'action_url' => "/reviews/{$this->review->id}",
        ];
    }
}
