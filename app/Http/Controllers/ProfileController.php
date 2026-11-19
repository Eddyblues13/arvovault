<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user's account settings.
     */
    public function edit(Request $request): View
    {
        return view('settings', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's name and email address.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('settings')->with('status', 'Profile updated.');
    }
}
