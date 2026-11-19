<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Admin;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportMessageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_read_a_conversation(): void
    {
        $response = $this->get(route('support.messages.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_conversation_shows_only_the_users_messages_and_marks_replies_as_read(): void
    {
        $user = User::factory()->create();
        SupportMessage::factory()->for($user)->create(['body' => 'Where is my payout?', 'created_at' => now()->subMinutes(5)]);
        $reply = SupportMessage::factory()->for($user)->fromAdmin()->create(['body' => 'It was sent this morning.', 'created_at' => now()->subMinute()]);
        SupportMessage::factory()->create(['body' => 'Another user question']);

        $response = $this->actingAs($user)->get(route('support.messages.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Where is my payout?', 'You', 'It was sent this morning.', 'Support']);
        $response->assertDontSee('Another user question');
        $this->assertNotNull($reply->refresh()->read_at);
    }

    public function test_empty_conversation_invites_the_user_to_write(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('support.messages.index'));

        $response->assertOk();
        $response->assertSee('How can we help?');
    }

    public function test_user_can_send_a_message_to_support(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('support.messages.store'), ['body' => 'Where is my payout?']);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('status', 'Message sent. Our team will reply in the chat.');
        $this->assertDatabaseHas('support_messages', [
            'user_id' => $user->id,
            'is_from_admin' => false,
            'admin_id' => null,
            'body' => 'Where is my payout?',
        ]);
    }

    public function test_chat_widget_receives_201_when_sending_with_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('support.messages.store'), ['body' => 'Hello there']);

        $response->assertCreated();
        $this->assertDatabaseHas('support_messages', ['user_id' => $user->id, 'body' => 'Hello there']);
    }

    public function test_user_cannot_send_a_message_as_the_support_team(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('support.messages.store'), [
            'body' => 'Your payout is approved',
            'is_from_admin' => '1',
            'admin_id' => Admin::factory()->create()->id,
        ]);

        $this->assertDatabaseHas('support_messages', [
            'user_id' => $user->id,
            'is_from_admin' => false,
            'admin_id' => null,
        ]);
    }

    public function test_empty_message_is_rejected(): void
    {
        $response = $this->actingAs(User::factory()->create())->postJson(route('support.messages.store'), ['body' => '']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['body' => 'The message field is required.']);
        $this->assertDatabaseCount('support_messages', 0);
    }

    public function test_message_longer_than_2000_characters_is_rejected(): void
    {
        $response = $this->actingAs(User::factory()->create())->postJson(route('support.messages.store'), ['body' => str_repeat('a', 2001)]);

        $response->assertJsonValidationErrors(['body' => 'The message field must not be greater than 2000 characters.']);
        $this->assertDatabaseCount('support_messages', 0);
    }

    public function test_message_bodies_are_escaped(): void
    {
        $user = User::factory()->create();
        SupportMessage::factory()->for($user)->create(['body' => '<script>alert(1)</script>']);

        $response = $this->actingAs($user)->get(route('support.messages.index'));

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_chat_button_shows_how_many_replies_are_unread(): void
    {
        $user = User::factory()->create();
        SupportMessage::factory()->for($user)->fromAdmin()->count(2)->create();
        SupportMessage::factory()->for($user)->fromAdmin()->read()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('2<span class="sr-only"> unread replies</span>', false);
    }
}
