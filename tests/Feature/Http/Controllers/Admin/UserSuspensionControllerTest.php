<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSuspensionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_suspend_a_user(): void
    {
        $this->travelTo('2026-11-20 10:00:00');
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.users.show', $user))
            ->post(route('admin.users.suspension.store', $user));

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('status', "Jane Doe's account is suspended.");
        $this->assertSame('2026-11-20 10:00:00', $user->refresh()->suspended_at->toDateTimeString());
    }

    public function test_suspending_again_keeps_the_original_suspension_date(): void
    {
        $user = User::factory()->create(['suspended_at' => '2026-11-01 08:00:00']);

        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.suspension.store', $user));

        $this->assertSame('2026-11-01 08:00:00', $user->refresh()->suspended_at->toDateTimeString());
    }

    public function test_admin_can_reactivate_a_suspended_user(): void
    {
        $user = User::factory()->suspended()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')
            ->from(route('admin.users.show', $user))
            ->delete(route('admin.users.suspension.destroy', $user));

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('status', "Jane Doe's account is active again.");
        $this->assertNull($user->refresh()->suspended_at);
    }

    public function test_user_page_shows_the_suspension_and_offers_to_reactivate(): void
    {
        $user = User::factory()->suspended()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.show', $user));

        $response->assertSee('Suspended since');
        $response->assertSee(route('admin.users.suspension.destroy', $user));
    }

    public function test_users_list_marks_suspended_accounts(): void
    {
        User::factory()->suspended()->create(['name' => 'Blocked Member']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.index'));

        $response->assertSeeInOrder(['Blocked Member', 'Suspended']);
    }

    public function test_suspended_user_is_signed_out_on_their_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.users.suspension.store', $user));

        $response = $this->actingAs($user->refresh(), 'web')->get(route('dashboard'));

        $response->assertRedirect(route('login'));
        $this->assertGuest('web');
    }
}
