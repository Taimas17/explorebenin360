<?php

namespace App\Mail;

use App\Models\MessageThread;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public MessageThread $thread;
    public Message $message;

    public function __construct(MessageThread $thread, Message $message)
    {
        $this->thread = $thread->load('traveler', 'provider');
        $this->message = $message->load('sender');
    }

    public function build()
    {
        $subject = __('New message') . ': ' . $this->thread->subject;
        return $this->subject($subject)
            ->view('emails.message_received', [
                'thread' => $this->thread,
                'message' => $this->message,
            ]);
    }
}
