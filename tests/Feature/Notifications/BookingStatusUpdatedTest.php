<?php

namespace Tests\Feature\Notifications;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BookingStatusUpdatedTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['confirmed', 'Your booking for Nov 24, 2026 at 3:30 PM UTC was confirmed.'])]
    #[TestWith(['cancelled', 'Your booking for Nov 24, 2026 at 3:30 PM UTC was cancelled.'])]
    public function test_notification_describes_the_booking_and_its_new_status(string $status, string $expectedMessage): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->for($user)->create(['status' => $status, 'scheduled_for' => '2026-11-24 15:30:00']);

        $user->notify(new BookingStatusUpdated($booking));

        $notification = $user->notifications()->sole();
        $this->assertSame($expectedMessage, $notification->data['message']);
        $this->assertSame(route('bookings'), $notification->data['url']);
    }
}
