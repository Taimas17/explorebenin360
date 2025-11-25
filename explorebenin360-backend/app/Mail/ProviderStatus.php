<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProviderStatus extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $status;
    public ?string $reason;

    public function __construct(User $user, string $status, ?string $reason = null)
    {
        $this->user = $user;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function build()
    {
        $subject = $this->status === 'approved' 
            ? __('Your provider account has been approved')
            : __('Your provider application has been rejected');
        
        return $this->subject($subject)
            ->view('emails.provider_status', [
                'user' => $this->user,
                'status' => $this->status,
                'reason' => $this->reason,
            ]);
    }
}
