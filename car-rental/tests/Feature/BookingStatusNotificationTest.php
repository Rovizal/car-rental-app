<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;

class BookingStatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_status_change_sends_notification()
    {
        Notification::fake();

        $user = User::factory()->create();
        $car = Car::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'status' => 'pending'
        ]);

        $response = $this->patchJson('/api/bookings/' . $booking->id . '/status', [
            'status' => 'completed'
        ]);

        $response->assertStatus(200);
        Notification::assertSentTo($user, BookingStatusNotification::class);
    }
}
