<?php

namespace Database\Factories;


use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        $brands = [
            'Toyota'        => ['Avanza', 'Fortuner', 'Innova', 'Yaris'],
            'Honda'         => ['Jazz', 'Civic', 'Brio', 'CR-V'],
            'Ford'          => ['Focus', 'Ranger', 'Mustang'],
            'BMW'           => ['320i', 'X1', 'X5', 'M4'],
            'Mercedes'      => ['A-Class', 'C-Class', 'GLA', 'S-Class'],
            'Nissan'        => ['Livina', 'X-Trail', 'March'],
            'Hyundai'       => ['Kona', 'Tucson', 'Santa Fe', 'Creta', 'Ioniq'],
            'Kia'           => ['Picanto', 'Sportage', 'Rio'],
            'Volkswagen'    => ['Polo', 'Golf', 'Tiguan'],
            'Mazda'         => ['CX-3', 'CX-5', 'Mazda3'],
            'Chevrolet'     => ['Spark', 'Trailblazer', 'Captiva'],
        ];

        $brand  = array_rand($brands);
        $name   = $brands[$brand][array_rand($brands[$brand])];

        return [
            'name'                  => $name,
            'brand'                 => $brand,
            'price_per_day'         => fake()->numberBetween(250000, 900000),
            'availability_status'   => fake()->randomElement(['available', 'booked']),
        ];
    }
}
