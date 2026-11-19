<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TipRequest;
use App\Models\Tip;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TipController extends Controller
{
    /**
     * List every tip, newest first.
     */
    public function index(): View
    {
        return view('admin.tips.index', [
            'tips' => Tip::query()->latest()->latest('id')->paginate(20),
        ]);
    }

    /**
     * Show the form for writing a tip.
     */
    public function create(): View
    {
        return view('admin.tips.create');
    }

    /**
     * Create a tip.
     */
    public function store(TipRequest $request): RedirectResponse
    {
        Tip::create($request->tipAttributes());

        return redirect()->route('admin.tips.index')->with('status', 'Tip saved.');
    }

    /**
     * Show the form for editing a tip.
     */
    public function edit(Tip $tip): View
    {
        return view('admin.tips.edit', [
            'tip' => $tip,
        ]);
    }

    /**
     * Update a tip.
     */
    public function update(TipRequest $request, Tip $tip): RedirectResponse
    {
        $tip->update($request->tipAttributes($tip));

        return redirect()->route('admin.tips.index')->with('status', 'Tip updated.');
    }

    /**
     * Delete a tip.
     */
    public function destroy(Tip $tip): RedirectResponse
    {
        $tip->delete();

        return redirect()->route('admin.tips.index')->with('status', 'Tip deleted.');
    }
}
