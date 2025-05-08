<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_book_a_car()
    {
        $user = User::factory()->create();
        $car = Car::factory()->create(['availability_status' => 'available']);

        $start = Carbon::now()->addDay()->toDateString();       // tomorrow
        $end   = Carbon::now()->addDays(3)->toDateString();     // 3 days from now

        $response = $this->postJson('/api/bookings', [
            'user_id'    => $user->id,
            'car_id'     => $car->id,
            'start_date' => $start,
            'end_date'   => $end,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'car_id'  => $car->id,
            'status'  => 'confirmed',
        ]);
    }

    public function test_cannot_book_if_car_already_booked_on_same_date()
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create(['availability_status' => 'booked']);

        $existingStart = Carbon::now()->addDays(5);
        $existingEnd   = Carbon::now()->addDays(10);

        // booking pertama
        Booking::factory()->create([
            'user_id'    => $user->id,
            'car_id'     => $car->id,
            'start_date' => $existingStart->toDateString(),
            'end_date'   => $existingEnd->toDateString(),
            'status'     => 'confirmed',
        ]);

        // booking kedua (overlap)
        $conflictStart = Carbon::now()->addDays(7)->toDateString();
        $conflictEnd   = Carbon::now()->addDays(12)->toDateString();

        $response = $this->postJson('/api/bookings', [
            'user_id'    => $user->id,
            'car_id'     => $car->id,
            'start_date' => $conflictStart,
            'end_date'   => $conflictEnd,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Car is not available during selected dates'
        ]);
    }

    public function test_validation_fails_for_invalid_dates()
    {
        $user = User::factory()->create();
        $car  = Car::factory()->create();

        $start = Carbon::now()->addDays(5)->toDateString();
        $end   = Carbon::now()->addDays(2)->toDateString(); // sebelum start

        $response = $this->postJson('/api/bookings', [
            'user_id'    => $user->id,
            'car_id'     => $car->id,
            'start_date' => $start,
            'end_date'   => $end,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }
}
