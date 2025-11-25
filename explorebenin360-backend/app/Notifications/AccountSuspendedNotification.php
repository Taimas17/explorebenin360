<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccountSuspendedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $reason)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
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

    public function toMail($notifiable)
    {
        return (new \App\Mail\AccountSuspended($notifiable, $this->reason))
            ->to($notifiable->email);
    }
}
