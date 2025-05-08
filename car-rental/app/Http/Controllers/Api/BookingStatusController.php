<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;

class BookingStatusController extends Controller
{
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,canceled'
        ]);

        $booking->update(['status' => $request->status]);

        if (in_array($request->status, ['canceled', 'completed'])) {
            $hasOtherBooking = Booking::where('car_id', $booking->car_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '!=', $booking->id)
                ->exists();

            if (!$hasOtherBooking) {
                $booking->car->update(['availability_status' => 'available']);
            }
        } elseif (in_array($request->status, ['pending', 'confirmed'])) {
            $booking->car->update(['availability_status' => 'booked']);
        }

        $booking->load('user', 'car');
        if ($booking->user) {
            $booking->user->notify(new BookingStatusNotification($booking));
        }

        return response()->json(['message' => 'Status updated and notification sent']);
    }
}
