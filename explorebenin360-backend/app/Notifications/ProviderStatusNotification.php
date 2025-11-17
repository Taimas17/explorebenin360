<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProviderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $rejectionReason = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
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
}
