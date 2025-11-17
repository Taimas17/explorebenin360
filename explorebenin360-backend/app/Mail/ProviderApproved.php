<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProviderApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $provider)
    {
    }

    public function build()
    {
        return $this->subject('Votre compte provider a été approuvé - ExploreBenin360')
            ->view('emails.provider_approved');
    }
}
