<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Relasi 'user'
        $query = \App\Models\Booking::with('car', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort_by')) {
            $query->orderBy($request->input('sort_by'), $request->input('sort_order', 'asc'));
        }

        $bookings = $query->paginate($request->input('per_page', 10));

        // Menambahkan nama user ke dalam response
        $bookings->getCollection()->transform(function ($booking) {
            $booking->user_name = $booking->user ? $booking->user->name : null; // Ambil nama user
            return $booking;
        });

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'car_id'     => 'required|exists:cars,id',
            'start_date' => ['required', 'date', 'after_or_equal:' . Carbon::today()->toDateString()],
            'end_date'   => 'required|date|after:start_date',
        ]);

        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);

        $carbooked = Booking::where('car_id', $data['car_id'])
            ->whereIn('status', ['confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<', $start)
                            ->where('end_date', '>', $end);
                    });
            })
            ->exists();

        if ($carbooked) {
            return response()->json(['message' => 'Car is not available during selected dates'], 422);
        }

        DB::beginTransaction();

        try {
            $car = Car::find($data['car_id']);

            if (!$car) {
                DB::rollBack();
                return response()->json(['message' => 'No Car found!'], 400);
            }

            $duration   = $start->diffInDays($end);
            $totalPrice = $car->price_per_day * $duration;

            $booking = Booking::create([
                'user_id'     => $data['user_id'],
                'car_id'      => $data['car_id'],
                'start_date'  => $start,
                'end_date'    => $end,
                'total_price' => $totalPrice,
                'status'      => 'pending',
            ]);

            // if ($car->availability_status !== 'booked') {
            //     $car->update(['availability_status' => 'booked']);
            // }

            DB::commit();

            $booking->load('car');
            Cache::forget('available_cars');

            return response()->json($booking, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create booking',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
