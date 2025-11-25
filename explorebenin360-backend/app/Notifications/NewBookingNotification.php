<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'offering_title' => $this->booking->offering->title,
            'user_name' => $this->booking->user->name,
            'amount' => $this->booking->amount,
            'currency' => $this->booking->currency,
            'message' => sprintf(
                'Nouvelle réservation pour "%s" par %s',
                $this->booking->offering->title,
                $this->booking->user->name
            ),
            'action_url' => "/provider/reservations/{$this->booking->id}",
        ];
    }

    public function toMail($notifiable)
    {
        return (new \App\Mail\NewBookingProvider($this->booking))
            ->to($notifiable->email);
    }
}
