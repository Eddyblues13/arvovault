<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_every_plan_with_its_visibility(): void
    {
        Plan::factory()->create(['name' => 'Starter', 'price_in_cents' => 900]);
        Plan::factory()->inactive()->create(['name' => 'Legacy', 'price_in_cents' => 1_900]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.plans.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Starter', 'Visible', '$9.00', 'Legacy', 'Hidden', '$19.00']);
    }

    public function test_create_page_renders(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.plans.create'));

        $response->assertOk();
        $response->assertSee('New plan');
    }

    public function test_store_creates_a_plan_with_one_feature_per_line(): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.plans.store'), [
            'name' => 'Pro',
            'price' => '49.99',
            'description' => 'For heavy users.',
            'features' => "Priority payouts\r\n\r\n  Dedicated support  \n",
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.plans.index'));
        $response->assertSessionHas('status', 'Plan created.');
        $plan = Plan::sole();
        $this->assertSame('Pro', $plan->name);
        $this->assertSame(4_999, $plan->price_in_cents);
        $this->assertSame('For heavy users.', $plan->description);
        $this->assertSame(['Priority payouts', 'Dedicated support'], $plan->features);
        $this->assertTrue($plan->is_active);
    }

    public function test_store_keeps_the_plan_hidden_when_visibility_is_unchecked(): void
    {
        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.plans.store'), [
            'name' => 'Pro',
            'price' => '49',
            'is_active' => '0',
        ]);

        $plan = Plan::sole();
        $this->assertFalse($plan->is_active);
        $this->assertSame([], $plan->features);
    }

    /**
     * Plan fields the form must refuse, with the message the admin sees.
     *
     * @return array<string, array{array<string, string>, string, string}>
     */
    public static function invalidPlans(): array
    {
        return [
            'missing name' => [['name' => ''], 'name', 'The name field is required.'],
            'name too long' => [['name' => str_repeat('a', 101)], 'name', 'The name field must not be greater than 100 characters.'],
            'missing price' => [['price' => ''], 'price', 'The price field is required.'],
            'negative price' => [['price' => '-1'], 'price', 'The price field must be at least 0.'],
            'fraction of a cent' => [['price' => '9.999'], 'price', 'The price field must have 0-2 decimal places.'],
            'price over the limit' => [['price' => '1000000.01'], 'price', 'The price field must not be greater than 1000000.'],
            'description too long' => [['description' => str_repeat('a', 1001)], 'description', 'The description field must not be greater than 1000 characters.'],
        ];
    }

    /**
     * @param  array<string, string>  $invalidField
     */
    #[DataProvider('invalidPlans')]
    public function test_invalid_plans_are_rejected(array $invalidField, string $field, string $expectedMessage): void
    {
        $response = $this->actingAs(Admin::factory()->create(), 'admin')->post(route('admin.plans.store'), [
            'name' => 'Pro',
            'price' => '49',
            ...$invalidField,
        ]);

        $response->assertSessionHasErrors([$field => $expectedMessage]);
        $this->assertDatabaseCount('plans', 0);
    }

    public function test_edit_page_is_filled_with_the_current_plan(): void
    {
        $plan = Plan::factory()->create(['name' => 'Pro', 'price_in_cents' => 4_999, 'features' => ['Priority payouts', 'Dedicated support']]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.plans.edit', $plan));

        $response->assertOk();
        $response->assertSee('value="Pro"', false);
        $response->assertSee('value="49.99"', false);
        $response->assertSee("Priority payouts\nDedicated support");
    }

    public function test_update_changes_the_plan(): void
    {
        $plan = Plan::factory()->create(['name' => 'Pro', 'is_active' => true]);

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->put(route('admin.plans.update', $plan), [
            'name' => 'Pro Plus',
            'price' => '59',
            'features' => 'Everything in Pro',
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('admin.plans.index'));
        $response->assertSessionHas('status', 'Plan updated.');
        $plan->refresh();
        $this->assertSame('Pro Plus', $plan->name);
        $this->assertSame(5_900, $plan->price_in_cents);
        $this->assertSame(['Everything in Pro'], $plan->features);
        $this->assertFalse($plan->is_active);
    }

    public function test_destroy_deletes_the_plan(): void
    {
        $plan = Plan::factory()->create();

        $response = $this->actingAs(Admin::factory()->create(), 'admin')->delete(route('admin.plans.destroy', $plan));

        $response->assertRedirect(route('admin.plans.index'));
        $response->assertSessionHas('status', 'Plan deleted.');
        $this->assertModelMissing($plan);
    }
}
