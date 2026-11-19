<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * List every plan, cheapest first.
     */
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::query()->orderBy('price_in_cents')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for creating a plan.
     */
    public function create(): View
    {
        return view('admin.plans.create');
    }

    /**
     * Create a plan.
     */
    public function store(PlanRequest $request): RedirectResponse
    {
        Plan::create($request->planAttributes());

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    /**
     * Show the form for editing a plan.
     */
    public function edit(Plan $plan): View
    {
        return view('admin.plans.edit', [
            'plan' => $plan,
        ]);
    }

    /**
     * Update a plan.
     */
    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($request->planAttributes());

        return redirect()->route('admin.plans.index')->with('status', 'Plan updated.');
    }

    /**
     * Delete a plan.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', 'Plan deleted.');
    }
}
