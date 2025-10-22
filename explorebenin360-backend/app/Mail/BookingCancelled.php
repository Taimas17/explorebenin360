<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancelled extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('offering','user','offering.provider');
    }

    public function build()
    {
        $subject = __('Your booking has been cancelled');
        return $this->subject($subject)
            ->view('emails.booking_cancelled', ['booking' => $this->booking]);
    }
}
