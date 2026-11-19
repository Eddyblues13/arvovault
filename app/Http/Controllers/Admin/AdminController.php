<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * List every admin.
     */
    public function index(Request $request): View
    {
        return view('admin.admins.index', [
            'admins' => Admin::query()->orderBy('name')->orderBy('id')->get(),
            'currentAdmin' => $request->user('admin'),
        ]);
    }

    /**
     * Show the form for adding an admin.
     */
    public function create(): View
    {
        return view('admin.admins.create');
    }

    /**
     * Add an admin who can sign in to the admin area.
     */
    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $admin = Admin::create($request->validated());

        return redirect()->route('admin.admins.index')->with('status', "{$admin->name} can now sign in to the admin area.");
    }

    /**
     * Remove another admin's access. Admins cannot remove themselves.
     */
    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        if ($admin->is($request->user('admin'))) {
            return back()->with('error', "You can't remove your own admin account.");
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')->with('status', "{$admin->name} no longer has admin access.");
    }
}
