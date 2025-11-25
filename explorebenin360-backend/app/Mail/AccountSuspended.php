<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountSuspended extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $reason;

    public function __construct(User $user, string $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    public function build()
    {
        $subject = __('Your account has been suspended');
        return $this->subject($subject)
            ->view('emails.account_suspended', [
                'user' => $this->user,
                'reason' => $this->reason,
            ]);
    }
}
