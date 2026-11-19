<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reply_is_sent_as_the_signed_in_admin(): void
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.support.messages.store', $user), [
            'body' => 'Your payout was sent this morning.',
        ]);

        $response->assertRedirect(route('admin.support.show', $user));
        $response->assertSessionHas('status', 'Reply sent.');
        $this->assertDatabaseHas('support_messages', [
            'user_id' => $user->id,
            'is_from_admin' => true,
            'admin_id' => $admin->id,
            'body' => 'Your payout was sent this morning.',
            'read_at' => null,
        ]);
    }

    public function test_chat_receives_201_when_replying_with_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->postJson(route('admin.support.messages.store', $user), [
            'body' => 'Hello Jane',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('support_messages', ['user_id' => $user->id, 'body' => 'Hello Jane', 'is_from_admin' => true]);
    }

    public function test_empty_reply_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->postJson(route('admin.support.messages.store', $user), ['body' => '']);

        $response->assertJsonValidationErrors(['body' => 'The message field is required.']);
        $this->assertDatabaseCount('support_messages', 0);
    }

    public function test_refreshing_the_conversation_shows_new_messages_and_marks_them_as_read(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $question = SupportMessage::factory()->for($user)->create(['body' => 'Any update?']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.messages.index', $user));

        $response->assertOk();
        $response->assertSeeInOrder(['Any update?', 'Jane Doe']);
        $this->assertNotNull($question->refresh()->read_at);
    }

    public function test_user_sees_the_admin_reply_in_their_chat(): void
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin')->post(route('admin.support.messages.store', $user), ['body' => 'Your payout was sent.']);

        $response = $this->actingAs($user, 'web')->get(route('support.messages.index'));

        $response->assertSeeInOrder(['Your payout was sent.', 'Support']);
        $this->assertNotNull(SupportMessage::sole()->read_at);
    }
}
