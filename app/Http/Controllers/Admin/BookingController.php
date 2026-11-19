<?php

namespace App\Http\Controllers\Admin;

use App\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * List bookings by their scheduled time, latest first, optionally filtered by status.
     *
     * An unknown status filter is ignored, so the list falls back to every booking.
     */
    public function index(Request $request): View
    {
        $status = BookingStatus::tryFrom($request->string('status')->toString());

        $bookings = Booking::query()->with('user')->latest('scheduled_for')->latest('id');

        if ($status !== null) {
            $bookings->where('status', $status);
        }

        return view('admin.bookings.index', [
            'bookings' => $bookings->paginate(20)->withQueryString(),
            'status' => $status,
            'pendingCount' => Booking::query()->pending()->count(),
        ]);
    }
}
