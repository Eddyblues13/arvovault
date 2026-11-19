<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * List the plans that are available to users, cheapest first.
     */
    public function __invoke(): View
    {
        return view('plans', [
            'plans' => Plan::query()->active()->orderBy('price_in_cents')->orderBy('id')->get(),
        ]);
    }
}
