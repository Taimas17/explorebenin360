<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewApprovedNotification extends Notification
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
            'reviewable_title' => $title,
            'message' => sprintf('Votre avis sur "%s" a été approuvé', $title),
            'action_url' => "/reviews/{$this->review->id}",
        ];
    }
}
