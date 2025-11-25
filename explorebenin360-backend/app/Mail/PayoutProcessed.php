<?php

namespace App\Mail;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayoutProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public Payout $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout->load('provider', 'paymentMethod');
    }

    public function build()
    {
        $subject = $this->payout->status === 'completed'
            ? __('Your withdrawal has been processed')
            : __('Your withdrawal has failed');
        
        return $this->subject($subject)
            ->view('emails.payout_processed', ['payout' => $this->payout]);
    }
}
