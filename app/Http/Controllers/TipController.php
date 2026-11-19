<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Illuminate\View\View;

class TipController extends Controller
{
    /**
     * List published tips, newest first.
     */
    public function index(): View
    {
        return view('tips.index', [
            'tips' => Tip::query()->published()->latest('published_at')->latest('id')->paginate(9),
        ]);
    }

    /**
     * Show a published tip.
     */
    public function show(Tip $tip): View
    {
        abort_unless($tip->isPublished(), 404);

        return view('tips.show', [
            'tip' => $tip,
        ]);
    }
}
