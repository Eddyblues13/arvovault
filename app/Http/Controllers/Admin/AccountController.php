<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAccountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Show the signed-in admin's account settings.
     */
    public function edit(Request $request): View
    {
        return view('admin.account.edit', [
            'admin' => $request->user('admin'),
        ]);
    }

    /**
     * Update the signed-in admin's details, and their password when a new one is given.
     */
    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $admin = $request->user('admin');

        $admin->fill($request->safe()->only(['name', 'email']));

        if ($request->filled('password')) {
            $admin->password = $request->validated('password');
        }

        $admin->save();

        return redirect()->route('admin.account.edit')->with('status', 'Your account was updated.');
    }
}
