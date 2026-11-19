@use('App\Http\Requests\Admin\PlanRequest')

{{-- Fields shared by the create and edit plan forms. Expects $plan, which is null when creating. --}}
<div class="grid gap-5 sm:grid-cols-2">
    <x-input-field label="Plan name" name="name" :value="$plan?->name" maxlength="100" placeholder="e.g. Pro" required />

    <x-input-field label="Price (USD)" name="price" type="number" inputmode="decimal" step="0.01" min="0" :max="PlanRequest::MAX_PRICE" :value="$plan ? number_format($plan->price_in_cents / 100, 2, '.', '') : null" placeholder="0.00" required />
</div>

<x-textarea-field label="Description" name="description" rows="3" maxlength="1000" :value="$plan?->description" hint="Optional. A sentence or two about who the plan is for." />

<x-textarea-field label="Features" name="features" rows="5" maxlength="2000" :value="$plan ? implode(PHP_EOL, $plan->features ?? []) : null" placeholder="e.g. Priority payouts" hint="One feature per line." />

<label class="flex w-fit cursor-pointer items-start gap-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan?->is_active ?? true)) class="mt-0.5 size-4 rounded border-slate-300 accent-blue-600">
    <span>
        <span class="block text-sm font-medium text-slate-700 dark:text-slate-300">Show this plan to users</span>
        <span class="block text-xs text-slate-500 dark:text-slate-400">Hidden plans stay here until you show them again.</span>
    </span>
</label>
