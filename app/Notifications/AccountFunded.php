<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Number;

class AccountFunded extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Transaction $transaction) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array{message: string, url: string}
     */
    public function toArray(object $notifiable): array
    {
        $amount = Number::currency($this->transaction->amount_in_cents / 100);

        return [
            'message' => $this->transaction->description
                ? "{$amount} was added to your balance: {$this->transaction->description}."
                : "{$amount} was added to your balance.",
            'url' => route('wallet'),
        ];
    }
}
