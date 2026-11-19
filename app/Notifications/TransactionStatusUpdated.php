<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\TransactionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Number;

class TransactionStatusUpdated extends Notification
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
        return [
            'message' => $this->message(),
            'url' => route('wallet'),
        ];
    }

    /**
     * Describe the outcome in a sentence the user can read at a glance.
     */
    private function message(): string
    {
        $amount = Number::currency($this->transaction->amount_in_cents / 100);
        $isApproved = $this->transaction->status === TransactionStatus::Completed;

        if ($this->transaction->isCredit()) {
            $credit = $this->transaction->method
                ? "Your deposit of {$amount} via {$this->transaction->method}"
                : "Your credit of {$amount}";

            return $isApproved
                ? "{$credit} was approved and added to your balance."
                : "{$credit} was declined.";
        }

        $withdrawal = $this->transaction->method
            ? "Your withdrawal of {$amount} to {$this->transaction->method}"
            : "Your withdrawal of {$amount}";

        return $isApproved
            ? "{$withdrawal} was approved."
            : "{$withdrawal} was declined, and the funds are back in your balance.";
    }
}
