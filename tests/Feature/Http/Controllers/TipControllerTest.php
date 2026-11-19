<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Tip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TipControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('tips'));

        $response->assertRedirect(route('login'));
    }

    public function test_published_tips_are_listed_newest_first_and_drafts_are_hidden(): void
    {
        Tip::factory()->create(['title' => 'Older tip', 'published_at' => now()->subDays(3)]);
        Tip::factory()->create(['title' => 'Newer tip', 'published_at' => now()->subDay()]);
        Tip::factory()->draft()->create(['title' => 'Unfinished tip']);

        $response = $this->actingAs(User::factory()->create())->get(route('tips'));

        $response->assertOk();
        $response->assertSeeInOrder(['Newer tip', 'Older tip']);
        $response->assertDontSee('Unfinished tip');
    }

    public function test_empty_list_says_tips_are_on_the_way(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('tips'));

        $response->assertOk();
        $response->assertSee('No tips yet');
    }

    public function test_published_tip_can_be_read_in_full(): void
    {
        $tip = Tip::factory()->create(['title' => 'Get paid faster', 'body' => "Verify your payout details.\nThen request early."]);

        $response = $this->actingAs(User::factory()->create())->get(route('tips.show', $tip));

        $response->assertOk();
        $response->assertSee('Get paid faster');
        $response->assertSee("Verify your payout details.\nThen request early.");
    }

    public function test_draft_tip_returns_404(): void
    {
        $tip = Tip::factory()->draft()->create();

        $response = $this->actingAs(User::factory()->create())->get(route('tips.show', $tip));

        $response->assertNotFound();
    }

    public function test_tip_content_is_escaped(): void
    {
        $tip = Tip::factory()->create(['title' => 'Safe title', 'body' => '<script>alert(1)</script>']);

        $response = $this->actingAs(User::factory()->create())->get(route('tips.show', $tip));

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }
}
