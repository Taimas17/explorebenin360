<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusChangeNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking, public string $previousStatus)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $messages = [
            'authorized' => 'Votre réservation "%s" a été autorisée',
            'cancelled' => 'Votre réservation "%s" a été annulée',
            'refunded' => 'Votre réservation "%s" a été remboursée',
        ];

        $message = sprintf(
            $messages[$this->booking->status] ?? 'Statut de réservation mis à jour',
            $this->booking->offering->title
        );

        return [
            'booking_id' => $this->booking->id,
            'offering_title' => $this->booking->offering->title,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->booking->status,
            'message' => $message,
            'action_url' => "/dashboard/reservations/{$this->booking->id}",
        ];
    }
}
