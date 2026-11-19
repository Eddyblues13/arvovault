<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_is_filled_with_the_admins_details(): void
    {
        $admin = Admin::factory()->create(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.account.edit'));

        $response->assertOk();
        $response->assertSee('value="Ada Lovelace"', false);
        $response->assertSee('value="ada@example.com"', false);
    }

    public function test_admin_can_update_their_details_and_keep_their_password(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.account.update'), [
            'name' => 'Ada King',
            'email' => 'ada.king@example.com',
        ]);

        $response->assertRedirect(route('admin.account.edit'));
        $response->assertSessionHas('status', 'Your account was updated.');
        $admin->refresh();
        $this->assertSame('Ada King', $admin->name);
        $this->assertSame('ada.king@example.com', $admin->email);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_admin_can_change_their_password_with_the_current_one(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->put(route('admin.account.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $this->assertTrue(Hash::check('new-secret-password', $admin->refresh()->password));
    }

    public function test_changing_the_password_requires_the_current_one(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.account.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertSessionHasErrors(['current_password' => 'The current password field is required when password is present.']);
        $this->assertTrue(Hash::check('password', $admin->refresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.account.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'current_password' => 'not-my-password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertSessionHasErrors(['current_password' => 'The password is incorrect.']);
        $this->assertTrue(Hash::check('password', $admin->refresh()->password));
    }

    public function test_email_used_by_another_admin_is_rejected(): void
    {
        Admin::factory()->create(['email' => 'taken@example.com']);
        $admin = Admin::factory()->create(['email' => 'ada@example.com']);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.account.update'), [
            'name' => $admin->name,
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertSame('ada@example.com', $admin->refresh()->email);
    }
}
