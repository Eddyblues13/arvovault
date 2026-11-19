<?php

namespace App\Http\Controllers\Admin;

use App\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Models\Booking;
use App\Notifications\BookingStatusUpdated;
use Illuminate\Http\RedirectResponse;

class BookingStatusController extends Controller
{
    /**
     * Confirm a pending booking, or cancel a booking that is not cancelled yet, and tell the user.
     *
     * The allowed starting statuses are part of the update query, so when two admins act on
     * the same booking at once, only the first change is applied.
     */
    public function update(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $status = $request->enum('status', BookingStatus::class);

        $allowedCurrentStatuses = $status === BookingStatus::Confirmed
            ? [BookingStatus::Pending]
            : [BookingStatus::Pending, BookingStatus::Confirmed];

        $updatedCount = Booking::query()
            ->whereKey($booking)
            ->whereIn('status', $allowedCurrentStatuses)
            ->update(['status' => $status]);

        if ($updatedCount === 0) {
            return back()->with('error', 'This booking can no longer be changed.');
        }

        $booking->refresh();
        $booking->user->notify(new BookingStatusUpdated($booking));

        return back()->with('status', $status === BookingStatus::Confirmed ? 'Booking confirmed.' : 'Booking cancelled.');
    }
}
