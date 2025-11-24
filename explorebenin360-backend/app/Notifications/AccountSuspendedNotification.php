<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountSuspendedNotification extends Notification
{
    use Queueable;

    public function __construct(public string $reason)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Votre compte a été suspendu.',
            'reason' => $this->reason,
            'suspended_at' => now(),
            'action_url' => '/contact',
        ];
    }
}
