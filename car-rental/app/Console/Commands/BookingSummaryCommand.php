<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class BookingSummaryCommand extends Command
{
    protected $signature    = 'booking:summary';
    protected $description  = 'Show daily booking summary';

    public function handle(): void
    {
        $today = Carbon::today();

        $bookings = Booking::whereDate('created_at', $today)->get();

        $total = $bookings->count();
        $byStatus = $bookings->groupBy('status')->map->count();
        $revenue = $bookings->where('status', 'completed')->sum('total_price');

        $this->info("Booking Summary for " . $today->toDateString());
        $this->line("Total Bookings: {$total}");
        foreach ($byStatus as $status => $count) {
            $this->line(" - {$status}: {$count}");
        }
        $this->line("Total Revenue (completed): Rp " . number_format($revenue, 0, ',', '.'));
    }
}
