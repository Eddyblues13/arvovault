<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List users newest first, optionally filtered by name or email.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $users = User::query()
            ->withAvailableBalance()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%");
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    /**
     * Show a user's balances and transaction history.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user,
            'availableBalanceInCents' => $user->availableBalanceInCents(),
            'totalCreditedInCents' => $user->totalCreditedInCents(),
            'totalWithdrawnInCents' => $user->totalWithdrawnInCents(),
            'transactions' => $user->transactions()->with('admin')->latest()->latest('id')->paginate(10),
        ]);
    }

    /**
     * Show the form for editing a user's account details.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update a user's account details, and their password when a new one is given.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->safe()->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->password = $request->validated('password');
        }

        $user->save();

        return redirect()->route('admin.users.show', $user)->with('status', 'User details updated.');
    }
}
