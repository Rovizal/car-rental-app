<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\BookingStatusNotification;

class BookingStatusController extends Controller
{
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,canceled'
        ]);

        DB::beginTransaction();

        try {
            $booking->update(['status' => $request->status]);

            if (in_array($request->status, ['canceled', 'completed'])) {
                $hasOtherBooking = Booking::where('car_id', $booking->car_id)
                    ->whereIn('status', ['confirmed'])
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if (!$hasOtherBooking && $booking->car) {
                    $booking->car->update(['availability_status' => 'available']);
                }
            } elseif ($request->status === 'confirmed') {
                if ($booking->car) {
                    Booking::where('car_id', $booking->car_id)
                        ->where('status', 'pending')
                        ->where('id', '!=', $booking->id)
                        ->update(['status' => 'canceled']);

                    $booking->car->update(['availability_status' => 'booked']);
                }
            }

            $booking->load('user', 'car');

            if ($booking->user) {
                $booking->user->notify(new BookingStatusNotification($booking));
            }

            DB::commit();

            return response()->json(['message' => 'Status updated and notification sent']);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
