<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReviewSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Review $review)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'comment_preview' => \Illuminate\Support\Str::limit($this->review->comment, 80),
            'message' => sprintf(
                'Nouvel avis reçu : %s étoiles',
                $this->review->rating
            ),
        ];
    }

    public function toMail($notifiable)
    {
        return (new \App\Mail\ReviewSubmitted($this->review, $notifiable))
            ->to($notifiable->email);
    }
}
