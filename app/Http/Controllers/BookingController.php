<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Show the booking request form with the user's upcoming and past bookings.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('bookings', [
            'upcomingBookings' => $user->bookings()->where('scheduled_for', '>=', now())->oldest('scheduled_for')->oldest('id')->get(),
            'pastBookings' => $user->bookings()->where('scheduled_for', '<', now())->latest('scheduled_for')->latest('id')->limit(10)->get(),
        ]);
    }

    /**
     * Request a booking, which waits for an admin to confirm it.
     */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $request->user()->bookings()->create($request->validated());

        return redirect()->route('bookings')->with('status', "Booking requested. We'll let you know as soon as it's confirmed.");
    }
}
