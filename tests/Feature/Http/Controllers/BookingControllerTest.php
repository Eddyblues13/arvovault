<?php

namespace Tests\Feature\Http\Controllers;

use App\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('bookings'));

        $response->assertRedirect(route('login'));
    }

    public function test_page_splits_the_users_bookings_into_upcoming_and_past(): void
    {
        $this->travelTo('2026-11-20 12:00:00');
        $user = User::factory()->create();
        Booking::factory()->for($user)->create(['topic' => 'Later session', 'scheduled_for' => '2026-11-25 15:00:00']);
        Booking::factory()->for($user)->confirmed()->create(['topic' => 'Sooner session', 'scheduled_for' => '2026-11-21 09:00:00']);
        Booking::factory()->for($user)->create(['topic' => 'Finished session', 'scheduled_for' => '2026-11-10 10:00:00']);
        Booking::factory()->create(['topic' => 'Another user session', 'scheduled_for' => '2026-11-22 10:00:00']);

        $response = $this->actingAs($user)->get(route('bookings'));

        $response->assertOk();
        $response->assertSeeInOrder(['Upcoming', 'Sooner session', 'Confirmed', 'Later session', 'Pending', 'Past', 'Finished session']);
        $response->assertDontSee('Another user session');
    }

    public function test_valid_request_creates_a_pending_booking(): void
    {
        $this->travelTo('2026-11-20 12:00:00');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'scheduled_for' => '2026-11-24T15:30',
            'topic' => 'Help with a payout',
            'notes' => 'My PayPal email changed.',
        ]);

        $response->assertRedirect(route('bookings'));
        $response->assertSessionHas('status', "Booking requested. We'll let you know as soon as it's confirmed.");
        $booking = $user->bookings()->sole();
        $this->assertSame('2026-11-24 15:30:00', $booking->scheduled_for->toDateTimeString());
        $this->assertSame('Help with a payout', $booking->topic);
        $this->assertSame('My PayPal email changed.', $booking->notes);
        $this->assertSame(BookingStatus::Pending, $booking->status);
    }

    /**
     * Booking requests the form must refuse, with the message the user sees.
     *
     * @return array<string, array{array<string, string>, string, string}>
     */
    public static function invalidRequests(): array
    {
        return [
            'missing date' => [['scheduled_for' => ''], 'scheduled_for', 'Choose a date and time.'],
            'not a date' => [['scheduled_for' => 'next tuesday-ish'], 'scheduled_for', 'Choose a valid date and time.'],
            'in the past' => [['scheduled_for' => '2026-11-19T10:00'], 'scheduled_for', 'Choose a date and time in the future.'],
            'more than six months ahead' => [['scheduled_for' => '2027-06-01T10:00'], 'scheduled_for', 'Choose a date within the next 6 months.'],
            'missing topic' => [['topic' => ''], 'topic', 'The topic field is required.'],
            'topic too long' => [['topic' => str_repeat('a', 121)], 'topic', 'The topic field must not be greater than 120 characters.'],
            'notes too long' => [['notes' => str_repeat('a', 1001)], 'notes', 'The notes field must not be greater than 1000 characters.'],
        ];
    }

    /**
     * @param  array<string, string>  $invalidField
     */
    #[DataProvider('invalidRequests')]
    public function test_invalid_requests_are_rejected(array $invalidField, string $field, string $expectedMessage): void
    {
        $this->travelTo('2026-11-20 12:00:00');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('bookings.store'), [
            'scheduled_for' => '2026-11-24T15:30',
            'topic' => 'Help with a payout',
            ...$invalidField,
        ]);

        $response->assertSessionHasErrors([$field => $expectedMessage]);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_user_cannot_confirm_their_own_booking(): void
    {
        $this->travelTo('2026-11-20 12:00:00');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('bookings.store'), [
            'scheduled_for' => '2026-11-24T15:30',
            'topic' => 'Help with a payout',
            'status' => BookingStatus::Confirmed->value,
            'user_id' => User::factory()->create()->id,
        ]);

        $booking = $user->bookings()->sole();
        $this->assertSame(BookingStatus::Pending, $booking->status);
    }
}
