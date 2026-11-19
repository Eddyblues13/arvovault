<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_every_admin_and_marks_the_current_one(): void
    {
        $currentAdmin = Admin::factory()->create(['name' => 'Ada Lovelace']);
        Admin::factory()->create(['name' => 'Grace Hopper']);

        $response = $this->actingAs($currentAdmin, 'admin')->get(route('admin.admins.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Ada Lovelace', 'You', 'Grace Hopper']);
    }

    public function test_admin_can_add_another_admin(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.admins.store'), [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('status', 'Grace Hopper can now sign in to the admin area.');
        $admin = Admin::where('email', 'grace@example.com')->sole();
        $this->assertSame('Grace Hopper', $admin->name);
        $this->assertTrue(Hash::check('secret-password', $admin->password));
    }

    public function test_new_admin_can_sign_in(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.admins.store'), [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);
        $this->post(route('admin.logout'));

        $response = $this->post(route('admin.login.store'), ['email' => 'grace@example.com', 'password' => 'secret-password']);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(Admin::where('email', 'grace@example.com')->sole(), 'admin');
    }

    public function test_email_used_by_another_admin_is_rejected(): void
    {
        Admin::factory()->create(['email' => 'grace@example.com']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.admins.store'), [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertDatabaseCount('admins', 2);
    }

    public function test_password_must_be_confirmed(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.admins.store'), [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
        $this->assertDatabaseCount('admins', 1);
    }

    public function test_admin_can_remove_another_admin(): void
    {
        $otherAdmin = Admin::factory()->create(['name' => 'Grace Hopper']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->delete(route('admin.admins.destroy', $otherAdmin));

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('status', 'Grace Hopper no longer has admin access.');
        $this->assertModelMissing($otherAdmin);
    }

    public function test_admin_cannot_remove_their_own_account(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->from(route('admin.admins.index'))
            ->delete(route('admin.admins.destroy', $admin));

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('error', "You can't remove your own admin account.");
        $this->assertModelExists($admin);
    }
}
