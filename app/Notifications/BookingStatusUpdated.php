<?php

namespace App\Notifications;

use App\BookingStatus;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Booking $booking) {}

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
        $scheduledFor = $this->booking->scheduled_for->format('M j, Y \a\t g:i A T');
        $outcome = $this->booking->status === BookingStatus::Confirmed ? 'confirmed' : 'cancelled';

        return [
            'message' => "Your booking for {$scheduledFor} was {$outcome}.",
            'url' => route('bookings'),
        ];
    }
}
