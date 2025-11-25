<?php

namespace App\Notifications;

use App\Models\MessageThread;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public MessageThread $thread, public Message $message)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'thread_id' => $this->thread->id,
            'message_id' => $this->message->id,
            'sender_name' => $this->message->sender->name,
            'subject' => $this->thread->subject,
            'message_preview' => Str::limit($this->message->body, 80),
            'action_url' => "/dashboard/messages/{$this->thread->id}",
            'message' => sprintf(
                'Nouveau message de %s : %s',
                $this->message->sender->name,
                Str::limit($this->message->body, 80)
            ),
        ];
    }

    public function toMail($notifiable)
    {
        return (new \App\Mail\MessageReceived($this->thread, $this->message))
            ->to($notifiable->email);
    }
}
