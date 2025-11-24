<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RolesUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public array $roles)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => 'Vos rôles ont été mis à jour.',
            'new_roles' => $this->roles,
            'action_url' => '/profile',
        ];
    }
}
