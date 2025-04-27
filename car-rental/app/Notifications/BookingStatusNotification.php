<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Booking;

class BookingStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Status Updated')
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Your booking status has been updated to: ' . $this->booking->status)
            ->line('Car: ' . $this->booking->car->brand . ' ' . $this->booking->car->name)
            ->line('From: ' . $this->booking->start_date)
            ->line('To: ' . $this->booking->end_date)
            ->line('Thank you for using our car rental service.');
    }
}
