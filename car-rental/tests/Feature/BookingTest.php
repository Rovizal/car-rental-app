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

        $start = Carbon::now()->addDays(1)->toDateString();
        $end = Carbon::now()->addDays(3)->toDateString();

        $response = $this->postJson('/api/bookings', [
            'user_id' => $user->id,
            'car_id' => $car->id,
            'start_date' => $start,
            'end_date' => $end,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'user_id'   => $user->id,
            'car_id'    => $car->id,
            'status'    => 'confirmed',
        ]);
    }

    public function test_cannot_book_if_car_already_booked_on_same_date()
    {
        $user   = User::factory()->create();
        $car    = Car::factory()->create(['availability_status' => 'booked']);

        Booking::factory()->create([
            'user_id'       => $user->id,
            'car_id'        => $car->id,
            'start_date'    => '2025-05-01',
            'end_date'      => '2025-05-05',
            'status'        => 'confirmed',
        ]);

        $response = $this->postJson('/api/bookings', [
            'user_id'       => $user->id,
            'car_id'        => $car->id,
            'start_date'    => '2025-05-03',
            'end_date'      => '2025-05-06',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Car is not available during selected dates'
        ]);
    }

    public function test_validation_fails_for_invalid_dates()
    {
        $user   = User::factory()->create();
        $car    = Car::factory()->create();

        $response = $this->postJson('/api/bookings', [
            'user_id'       => $user->id,
            'car_id'        => $car->id,
            'start_date'    => '2025-05-05',
            'end_date'      => '2025-05-01',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    }
}
