<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Tip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TipControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_every_tip_with_its_status(): void
    {
        Tip::factory()->create(['title' => 'Published tip', 'published_at' => '2026-11-02 10:00:00', 'created_at' => now()->subDay()]);
        Tip::factory()->draft()->create(['title' => 'Draft tip', 'created_at' => now()]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.tips.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Draft tip', 'Draft', 'Published tip', 'Published Nov 2']);
    }

    public function test_store_publishes_the_tip_right_away_when_publish_is_checked(): void
    {
        $this->travelTo('2026-11-20 09:30:00');

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.tips.store'), [
            'title' => 'Get paid faster',
            'body' => 'Double-check your payout details.',
            'is_published' => '1',
        ]);

        $response->assertRedirect(route('admin.tips.index'));
        $response->assertSessionHas('status', 'Tip saved.');
        $tip = Tip::sole();
        $this->assertSame('Get paid faster', $tip->title);
        $this->assertSame('Double-check your payout details.', $tip->body);
        $this->assertSame('2026-11-20 09:30:00', $tip->published_at->toDateTimeString());
    }

    public function test_store_saves_a_draft_when_publish_is_unchecked(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.tips.store'), [
            'title' => 'Get paid faster',
            'body' => 'Double-check your payout details.',
            'is_published' => '0',
        ]);

        $this->assertNull(Tip::sole()->published_at);
    }

    public function test_store_rejects_missing_fields(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.tips.store'), []);

        $response->assertSessionHasErrors(['title' => 'The title field is required.', 'body' => 'The body field is required.']);
        $this->assertDatabaseCount('tips', 0);
    }

    public function test_store_rejects_a_title_longer_than_150_characters(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.tips.store'), [
            'title' => str_repeat('a', 151),
            'body' => 'Double-check your payout details.',
        ]);

        $response->assertSessionHasErrors(['title' => 'The title field must not be greater than 150 characters.']);
        $this->assertDatabaseCount('tips', 0);
    }

    public function test_updating_a_published_tip_keeps_its_original_publish_date(): void
    {
        $tip = Tip::factory()->create(['published_at' => '2026-11-02 10:00:00']);

        $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.tips.update', $tip), [
            'title' => 'Updated title',
            'body' => 'Updated body.',
            'is_published' => '1',
        ]);

        $tip->refresh();
        $this->assertSame('Updated title', $tip->title);
        $this->assertSame('2026-11-02 10:00:00', $tip->published_at->toDateTimeString());
    }

    public function test_unpublishing_a_tip_turns_it_back_into_a_draft(): void
    {
        $tip = Tip::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.tips.update', $tip), [
            'title' => $tip->title,
            'body' => $tip->body,
            'is_published' => '0',
        ]);

        $response->assertRedirect(route('admin.tips.index'));
        $response->assertSessionHas('status', 'Tip updated.');
        $this->assertNull($tip->refresh()->published_at);
    }

    public function test_edit_page_is_filled_with_the_current_tip(): void
    {
        $tip = Tip::factory()->create(['title' => 'Get paid faster', 'body' => 'Double-check your payout details.']);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.tips.edit', $tip));

        $response->assertOk();
        $response->assertSee('value="Get paid faster"', false);
        $response->assertSee('Double-check your payout details.');
    }

    public function test_destroy_deletes_the_tip(): void
    {
        $tip = Tip::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->delete(route('admin.tips.destroy', $tip));

        $response->assertRedirect(route('admin.tips.index'));
        $response->assertSessionHas('status', 'Tip deleted.');
        $this->assertModelMissing($tip);
    }
}
