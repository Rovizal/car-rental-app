<?php

namespace App\Http\Controllers\Api;

use App\Models\Car;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CarController extends Controller
{

    public function index(Request $request)
    {
        $isDatatables = $request->has('draw');

        $cacheKey = 'cars_' . md5(json_encode($request->all()));

        $cached = Cache::remember($cacheKey, 60, function () use ($request, $isDatatables) {
            $query = Car::query();

            if ($request->filled('brand')) {
                $query->where('brand', 'like', "%{$request->brand}%");
            }

            if ($request->filled('min_price') && $request->filled('max_price')) {
                $query->whereBetween('price_per_day', [
                    $request->min_price,
                    $request->max_price
                ]);
            }

            if ($isDatatables) {
                if ($request->filled('search.value')) {
                    $query->where('name', 'like', '%' . $request->input('search.value') . '%');
                }
            } elseif ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('availability_status')) {
                $query->where('availability_status', $request->availability_status);
            }

            if ($request->filled('sort_by')) {
                $query->orderBy($request->sort_by, $request->input('sort_order', 'asc'));
            } elseif (!$isDatatables) {
                $query->inRandomOrder();
            }

            $page = $isDatatables
                ? ($request->input('start') / $request->input('length')) + 1
                : $request->input('page', 1);

            $perPage = $isDatatables
                ? $request->input('length', 10)
                : $request->input('per_page', 10);

            $result = $query->paginate($perPage, ['*'], 'page', $page);

            // Tambahkan info is_booked untuk PrimeVue
            if (!$isDatatables) {
                $result->getCollection()->transform(function ($car) {
                    $car->is_booked = (bool) Booking::where('car_id', $car->id)
                        ->whereIn('status', ['pending', 'booked'])
                        ->exists();
                    return $car;
                });
            }

            return $result;
        });

        if ($isDatatables) {
            return response()->json([
                'draw'              => intval($request->input('draw')),
                'recordsTotal'      => $cached->total(),
                'recordsFiltered'   => $cached->total(),
                'data'              => $cached->items(),
            ]);
        }

        return response()->json($cached);
    }

    public function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        // Cek apakah car_id sudah ada di tabel bookings dan statusnya belum 'confirmed'
        $isBooked = \App\Models\Booking::where('car_id', $id)
            ->whereIn('status', ['pending', 'booked'])
            ->exists();

        return response()->json([
            'car'       => $car,
            'is_booked' => $isBooked
        ]);
    }
}
