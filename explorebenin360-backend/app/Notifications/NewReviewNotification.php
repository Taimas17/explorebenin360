<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
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
        $reviewable = $this->review->reviewable;
        $title = $reviewable->title ?? ($reviewable->name ?? '');
        return [
            'review_id' => $this->review->id,
            'user_name' => optional($this->review->user)->name,
            'rating' => $this->review->rating,
            'reviewable_title' => $title,
            'message' => sprintf('Nouvel avis sur "%s"', $title),
            'action_url' => "/reviews/{$this->review->id}",
        ];
    }
}
