<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProviderRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $provider,
        public string $reason
    ) {
    }

    public function build()
    {
        return $this->subject('Votre demande provider - ExploreBenin360')
            ->view('emails.provider_rejected');
    }
}
