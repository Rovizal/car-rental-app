<?php

use App\Models\Car;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(500)->create();
        Car::factory(1000)->create();
        Booking::factory(2000)->create();
    }
}
