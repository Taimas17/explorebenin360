<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PayoutProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payout $payout)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'payout_id' => $this->payout->id,
            'reference' => $this->payout->reference,
            'amount' => $this->payout->amount,
            'currency' => $this->payout->currency,
            'status' => $this->payout->status,
            'message' => $this->payout->status === 'completed'
                ? sprintf('Votre retrait de %s %s a été effectué', $this->payout->amount, $this->payout->currency)
                : sprintf('Votre retrait de %s %s a échoué', $this->payout->amount, $this->payout->currency),
            'action_url' => '/provider/earnings',
        ];
    }

    public function toMail($notifiable)
    {
        return (new \App\Mail\PayoutProcessed($this->payout))
            ->to($notifiable->email);
    }
}
