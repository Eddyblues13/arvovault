<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AccountFunded;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkNotificationsAsReadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_bell_lists_recent_notifications_and_counts_the_unread_ones(): void
    {
        $user = User::factory()->create();
        $user->notify(new AccountFunded(Transaction::factory()->for($user)->create(['amount_in_cents' => 2_500, 'description' => null])));

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('$25.00 was added to your balance.');
        $response->assertSee('1<span class="sr-only"> unread</span>', false);
        $response->assertSee('Mark all as read');
    }

    public function test_marking_all_as_read_clears_the_unread_count(): void
    {
        $user = User::factory()->create();
        $user->notify(new AccountFunded(Transaction::factory()->for($user)->create()));
        $user->notify(new AccountFunded(Transaction::factory()->for($user)->create()));

        $response = $this->actingAs($user)
            ->from(route('wallet'))
            ->post(route('notifications.mark-all-read'));

        $response->assertRedirect(route('wallet'));
        $this->assertSame(0, $user->unreadNotifications()->count());
        $this->assertSame(2, $user->notifications()->count());
    }

    public function test_marking_as_read_leaves_other_users_notifications_unread(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUser->notify(new AccountFunded(Transaction::factory()->for($otherUser)->create()));

        $this->actingAs($user)->post(route('notifications.mark-all-read'));

        $this->assertSame(1, $otherUser->unreadNotifications()->count());
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->post(route('notifications.mark-all-read'));

        $response->assertRedirect(route('login'));
    }
}
