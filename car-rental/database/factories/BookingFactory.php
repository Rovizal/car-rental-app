<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Car;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start      = fake()->dateTimeBetween('-15 days', '+15 days');
        $duration   = fake()->numberBetween(1, 7); // durasi maksimal 7 hari
        $end        = (clone $start)->modify("+{$duration} days");

        $car    = Car::inRandomOrder()->first() ?? Car::factory()->create();
        $user   = User::inRandomOrder()->first() ?? User::factory()->create();

        $totalPrice = $car->price_per_day * $duration;

        return [
            'user_id'       => $user->id,
            'car_id'        => $car->id,
            'start_date'    => $start,
            'end_date'      => $end,
            'total_price'   => $totalPrice,
            'status'        => fake()->randomElement(['pending', 'confirmed', 'completed', 'canceled']),
        ];
    }
}
