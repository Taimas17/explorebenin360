<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProviderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $rejectionReason = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        if ($this->status === 'approved') {
            return [
                'message' => 'Félicitations ! Votre compte provider a été approuvé',
                'action_url' => '/provider',
            ];
        }

        return [
            'message' => 'Votre demande provider a été rejetée',
            'reason' => $this->rejectionReason,
            'action_url' => '/become-provider',
        ];
    }

    public function toMail($notifiable)
    {
        return (new \App\Mail\ProviderStatus($notifiable, $this->status, $this->rejectionReason))
            ->to($notifiable->email);
    }
}
