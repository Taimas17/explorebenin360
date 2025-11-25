<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewBookingProvider extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('offering', 'offering.provider', 'user');
    }

    public function build()
    {
        $subject = __('New booking for your offer');
        return $this->subject($subject)
            ->view('emails.new_booking_provider', ['booking' => $this->booking]);
    }
}
