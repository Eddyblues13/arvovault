<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookings_from_every_user_are_listed_latest_scheduled_first(): void
    {
        Booking::factory()->for(User::factory()->create(['name' => 'Jane Doe']))->create(['topic' => 'Sooner session', 'scheduled_for' => now()->addDay()]);
        Booking::factory()->for(User::factory()->create(['name' => 'John Smith']))->create(['topic' => 'Later session', 'scheduled_for' => now()->addDays(5)]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.bookings.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Later session', 'John Smith', 'Sooner session', 'Jane Doe']);
    }

    public function test_status_filter_shows_only_matching_bookings(): void
    {
        Booking::factory()->create(['topic' => 'Waiting session']);
        Booking::factory()->cancelled()->create(['topic' => 'Cancelled session']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.bookings.index', ['status' => 'pending']));

        $response->assertSee('Waiting session');
        $response->assertDontSee('Cancelled session');
    }

    public function test_unknown_status_filter_is_ignored(): void
    {
        Booking::factory()->create(['topic' => 'Waiting session']);
        Booking::factory()->cancelled()->create(['topic' => 'Cancelled session']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.bookings.index', ['status' => 'archived']));

        $response->assertOk();
        $response->assertSee('Waiting session');
        $response->assertSee('Cancelled session');
    }

    public function test_cancelled_bookings_offer_no_actions(): void
    {
        $pendingBooking = Booking::factory()->create();
        $cancelledBooking = Booking::factory()->cancelled()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.bookings.index'));

        $response->assertSee(route('admin.bookings.status.update', $pendingBooking));
        $response->assertDontSee(route('admin.bookings.status.update', $cancelledBooking));
    }

    public function test_booking_notes_are_escaped(): void
    {
        Booking::factory()->create(['notes' => '<script>alert(1)</script>']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.bookings.index'));

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }
}
