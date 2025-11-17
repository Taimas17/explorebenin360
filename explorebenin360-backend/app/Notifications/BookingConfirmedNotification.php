<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'offering_title' => $this->booking->offering->title,
            'amount' => $this->booking->amount,
            'currency' => $this->booking->currency,
            'message' => sprintf(
                'Votre réservation "%s" a été confirmée',
                $this->booking->offering->title
            ),
            'action_url' => "/dashboard/reservations/{$this->booking->id}",
        ];
    }
}
