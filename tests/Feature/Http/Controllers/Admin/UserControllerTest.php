<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_listed_newest_first(): void
    {
        User::factory()->create(['name' => 'Older Member', 'created_at' => now()->subDays(2)]);
        User::factory()->create(['name' => 'Newer Member', 'created_at' => now()->subDay()]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('2 registered accounts');
        $response->assertSeeInOrder(['Newer Member', 'Older Member']);
    }

    public function test_list_shows_the_same_available_balance_as_the_users_own_dashboard(): void
    {
        $user = User::factory()->create();
        Transaction::factory()->for($user)->create(['amount_in_cents' => 50_000]);
        Transaction::factory()->for($user)->pending()->create(['amount_in_cents' => 9_900]);
        Transaction::factory()->for($user)->failed()->create(['amount_in_cents' => 7_000]);
        Transaction::factory()->for($user)->withdrawal()->create(['amount_in_cents' => 12_000]);
        Transaction::factory()->for($user)->withdrawal()->pending()->create(['amount_in_cents' => 3_000]);
        Transaction::factory()->for($user)->withdrawal()->failed()->create(['amount_in_cents' => 4_000]);
        Transaction::factory()->create(['amount_in_cents' => 80_000]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.index'));

        $response->assertViewHas('users', fn (LengthAwarePaginator $users): bool => $users->firstWhere('id', $user->id)->available_balance_in_cents === 35_000);
        $response->assertSee('$350.00');
        $this->assertSame(35_000, $user->availableBalanceInCents());
    }

    public function test_search_matches_users_by_name_or_email_ignoring_case(): void
    {
        User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);
        User::factory()->create(['name' => 'John Smith', 'email' => 'john@sample.org']);

        $admin = Admin::factory()->create();

        $byName = $this->actingAs($admin, 'admin')->get(route('admin.users.index', ['search' => 'JANE']));
        $byEmail = $this->actingAs($admin, 'admin')->get(route('admin.users.index', ['search' => 'sample.org']));

        $byName->assertSee('Jane Doe');
        $byName->assertDontSee('John Smith');
        $byName->assertSee('1 user matches');
        $byEmail->assertSee('John Smith');
        $byEmail->assertDontSee('Jane Doe');
    }

    public function test_search_without_matches_offers_to_clear_it(): void
    {
        User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.index', ['search' => 'nobody']));

        $response->assertOk();
        $response->assertSee('No users found');
        $response->assertSee('Clear search');
        $response->assertDontSee('Jane Doe');
    }

    public function test_list_shows_fifteen_users_per_page(): void
    {
        User::factory()->count(16)->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.index'));

        $response->assertViewHas('users', fn (LengthAwarePaginator $users): bool => $users->count() === 15 && $users->total() === 16);
    }

    public function test_search_term_is_escaped_when_shown_back(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.index', ['search' => '"><script>alert(1)</script>']));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_user_names_are_escaped_on_admin_pages(): void
    {
        $user = User::factory()->create(['name' => "<script>alert('xss')</script>"]);
        $admin = Admin::factory()->create();

        $listResponse = $this->actingAs($admin, 'admin')->get(route('admin.users.index'));
        $detailsResponse = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user));

        $listResponse->assertSee('&lt;script&gt;alert(', false);
        $listResponse->assertDontSee("<script>alert('xss')</script>", false);
        $detailsResponse->assertSee('&lt;script&gt;alert(', false);
        $detailsResponse->assertDontSee("<script>alert('xss')</script>", false);
    }

    public function test_user_page_shows_balances_and_only_that_users_transactions(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $admin = Admin::factory()->create(['name' => 'Ada Lovelace']);
        Transaction::factory()->for($user)->for($admin)->create(['amount_in_cents' => 25_000, 'description' => 'Welcome bonus']);
        Transaction::factory()->for($user)->withdrawal('Zelle')->create(['amount_in_cents' => 5_000]);
        Transaction::factory()->create(['description' => 'Another user bonus']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user));

        $response->assertOk();
        $response->assertSee('Jane Doe');
        $response->assertViewHas('availableBalanceInCents', 20_000);
        $response->assertViewHas('totalCreditedInCents', 25_000);
        $response->assertViewHas('totalWithdrawnInCents', 5_000);
        $response->assertSeeInOrder(['Withdrawal to Zelle', 'Welcome bonus']);
        $response->assertSee('Added by Ada Lovelace');
        $response->assertDontSee('Another user bonus');
    }

    public function test_missing_user_returns_404(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.show', 999));

        $response->assertNotFound();
    }

    public function test_edit_page_is_filled_with_the_users_current_details(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.users.edit', $user));

        $response->assertOk();
        $response->assertSee('value="Jane Doe"', false);
        $response->assertSee('value="jane@example.com"', false);
    }

    public function test_update_changes_name_and_email_and_keeps_the_password_when_left_blank(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $response->assertSessionHas('status', 'User details updated.');
        $user->refresh();
        $this->assertSame('Jane Smith', $user->name);
        $this->assertSame('jane.smith@example.com', $user->email);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_update_sets_a_new_password_when_one_is_given(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertRedirect(route('admin.users.show', $user));
        $this->assertTrue(Hash::check('new-secret-password', $user->refresh()->password));
    }

    public function test_update_rejects_missing_fields(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), []);

        $response->assertSessionHasErrors(['name' => 'The name field is required.', 'email' => 'The email field is required.']);
        $this->assertSame('Jane Doe', $user->refresh()->name);
    }

    /**
     * Account details the edit form must refuse, with the message the admin sees.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function invalidAccountDetails(): array
    {
        return [
            'name too long' => ['name', str_repeat('a', 256), 'The name field must not be greater than 255 characters.'],
            'malformed email' => ['email', 'not-an-email', 'The email field must be a valid email address.'],
            'email too long' => ['email', str_repeat('a', 244).'@example.com', 'The email field must not be greater than 255 characters.'],
        ];
    }

    #[DataProvider('invalidAccountDetails')]
    public function test_update_rejects_invalid_account_details(string $field, string $value, string $expectedMessage): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            $field => $value,
        ]);

        $response->assertSessionHasErrors([$field => $expectedMessage]);
        $user->refresh();
        $this->assertSame('Jane Doe', $user->name);
        $this->assertSame('jane@example.com', $user->email);
    }

    public function test_update_rejects_an_email_that_belongs_to_another_user(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
        $this->assertSame('jane@example.com', $user->refresh()->email);
    }

    public function test_update_rejects_an_email_with_uppercase_letters(): void
    {
        $user = User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => 'Jane@Example.com',
        ]);

        $response->assertSessionHasErrors(['email' => 'The email field must be lowercase.']);
        $this->assertSame('jane@example.com', $user->refresh()->email);
    }

    public function test_update_rejects_a_password_confirmation_that_does_not_match(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'new-secret-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field confirmation does not match.']);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }

    public function test_update_rejects_a_password_shorter_than_eight_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password' => 'The password field must be at least 8 characters.']);
        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }
}
