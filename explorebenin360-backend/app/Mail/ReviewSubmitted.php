<?php

namespace App\Mail;

use App\Models\Review;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public Review $review;
    public User $provider;

    public function __construct(Review $review, User $provider)
    {
        $this->review = $review->load('user');
        $this->provider = $provider;
    }

    public function build()
    {
        $subject = __('New review received for your offer');
        return $this->subject($subject)
            ->view('emails.review_submitted', [
                'review' => $this->review,
                'provider' => $this->provider,
            ]);
    }
}
