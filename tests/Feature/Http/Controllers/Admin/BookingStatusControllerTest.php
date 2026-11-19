<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\BookingStatus;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_a_pending_booking_tells_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $booking = Booking::factory()->for($user)->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.bookings.index'))
            ->patch(route('admin.bookings.status.update', $booking), ['status' => 'confirmed']);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('status', 'Booking confirmed.');
        $this->assertSame(BookingStatus::Confirmed, $booking->refresh()->status);
        Notification::assertSentTo($user, BookingStatusUpdated::class, fn (BookingStatusUpdated $notification): bool => $notification->booking->is($booking));
    }

    #[DataProvider('cancellableStatuses')]
    public function test_cancelling_a_booking_tells_the_user(string $currentStatus): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $booking = Booking::factory()->for($user)->create(['status' => $currentStatus]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.bookings.index'))
            ->patch(route('admin.bookings.status.update', $booking), ['status' => 'cancelled']);

        $response->assertSessionHas('status', 'Booking cancelled.');
        $this->assertSame(BookingStatus::Cancelled, $booking->refresh()->status);
        Notification::assertSentTo($user, BookingStatusUpdated::class);
    }

    /**
     * Statuses a booking can be cancelled from.
     *
     * @return array<string, array{string}>
     */
    public static function cancellableStatuses(): array
    {
        return [
            'pending' => ['pending'],
            'confirmed' => ['confirmed'],
        ];
    }

    /**
     * Status changes that are no longer possible, with the starting status.
     *
     * @return array<string, array{string, string}>
     */
    public static function impossibleChanges(): array
    {
        return [
            'confirming a confirmed booking' => ['confirmed', 'confirmed'],
            'confirming a cancelled booking' => ['cancelled', 'confirmed'],
            'cancelling a cancelled booking' => ['cancelled', 'cancelled'],
        ];
    }

    #[DataProvider('impossibleChanges')]
    public function test_a_booking_that_can_no_longer_change_keeps_its_status(string $currentStatus, string $requestedStatus): void
    {
        Notification::fake();
        $booking = Booking::factory()->create(['status' => $currentStatus]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.bookings.index'))
            ->patch(route('admin.bookings.status.update', $booking), ['status' => $requestedStatus]);

        $response->assertRedirect(route('admin.bookings.index'));
        $response->assertSessionHas('error', 'This booking can no longer be changed.');
        $this->assertSame($currentStatus, $booking->refresh()->status->value);
        Notification::assertNothingSent();
    }

    public function test_status_must_be_confirmed_or_cancelled(): void
    {
        $booking = Booking::factory()->confirmed()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->patch(route('admin.bookings.status.update', $booking), ['status' => 'pending']);

        $response->assertSessionHasErrors(['status' => 'The selected status is invalid.']);
        $this->assertSame(BookingStatus::Confirmed, $booking->refresh()->status);
    }

    public function test_signed_in_user_cannot_confirm_bookings(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->for($user)->create();

        $response = $this->actingAs($user)->patch(route('admin.bookings.status.update', $booking), ['status' => 'confirmed']);

        $response->assertRedirect(route('admin.login'));
        $this->assertSame(BookingStatus::Pending, $booking->refresh()->status);
    }
}
