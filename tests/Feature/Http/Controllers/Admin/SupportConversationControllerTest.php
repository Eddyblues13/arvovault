<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportConversationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_inbox_lists_conversations_with_the_most_recent_activity_first(): void
    {
        $jane = User::factory()->create(['name' => 'Jane Doe']);
        $john = User::factory()->create(['name' => 'John Smith']);
        SupportMessage::factory()->for($jane)->create(['body' => 'Older question', 'created_at' => now()->subHours(2)]);
        SupportMessage::factory()->for($john)->create(['body' => 'Newer question', 'created_at' => now()->subHour()]);
        User::factory()->create(['name' => 'Quiet User']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['John Smith', 'Newer question', 'Jane Doe', 'Older question']);
        $response->assertDontSee('Quiet User');
    }

    public function test_inbox_counts_unread_messages_and_marks_admin_replies_as_yours(): void
    {
        $user = User::factory()->create();
        SupportMessage::factory()->for($user)->count(2)->create(['created_at' => now()->subHours(2)]);
        SupportMessage::factory()->for($user)->fromAdmin()->create(['body' => 'We are on it.', 'created_at' => now()->subHour()]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.index'));

        $response->assertSee('2<span class="sr-only"> unread</span>', false);
        $response->assertSee('You: We are on it.');
    }

    public function test_empty_inbox_explains_where_conversations_come_from(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.index'));

        $response->assertOk();
        $response->assertSee('No conversations yet');
    }

    public function test_opening_a_conversation_marks_the_users_messages_as_read(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $question = SupportMessage::factory()->for($user)->create(['body' => 'Where is my payout?']);
        $reply = SupportMessage::factory()->for($user)->fromAdmin()->create(['body' => 'Sent this morning.']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.show', $user));

        $response->assertOk();
        $response->assertSeeInOrder(['Where is my payout?', 'Jane Doe', 'Sent this morning.']);
        $this->assertNotNull($question->refresh()->read_at);
        $this->assertNull($reply->refresh()->read_at);
    }

    public function test_admin_can_open_a_conversation_with_a_user_who_has_not_written_yet(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.show', $user));

        $response->assertOk();
        $response->assertSee('No messages yet');
    }

    public function test_conversation_messages_are_escaped(): void
    {
        $user = User::factory()->create();
        SupportMessage::factory()->for($user)->create(['body' => '<script>alert(1)</script>']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.support.show', $user));

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }
}
