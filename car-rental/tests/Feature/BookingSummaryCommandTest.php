<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;

class BookingSummaryCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_booking_summary()
    {
        $user   = User::factory()->create();
        $car    = Car::factory()->create();

        Booking::factory()->create([
            'user_id'       => $user->id,
            'car_id'        => $car->id,
            'start_date'    => now(),
            'end_date'      => now()->addDays(2),
            'status'        => 'completed',
            'total_price'   => 500000,
            'created_at'    => now(),
        ]);

        Booking::factory()->create([
            'user_id'       => $user->id,
            'car_id'        => $car->id,
            'start_date'    => now(),
            'end_date'      => now()->addDays(3),
            'status'        => 'pending',
            'total_price'   => 700000,
            'created_at'    => now(),
        ]);

        $this->artisan('booking:summary')
            ->expectsOutputToContain('Total Bookings: 2')
            ->expectsOutputToContain('completed: 1')
            ->expectsOutputToContain('pending: 1')
            ->expectsOutputToContain('Total Revenue')
            ->assertExitCode(0);
    }
}
