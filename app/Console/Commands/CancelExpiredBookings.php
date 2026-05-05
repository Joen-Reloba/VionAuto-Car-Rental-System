<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending bookings that have passed their rental start date without any activity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Find all pending bookings where rent_start date has already passed
            $expiredBookings = Booking::where('status', 'pending')
                ->where('rent_start', '<', Carbon::today())
                ->get();

            if ($expiredBookings->isEmpty()) {
                $this->info('No expired bookings found.');
                return Command::SUCCESS;
            }

            $count = 0;

            foreach ($expiredBookings as $booking) {
                // Additional check: ensure the vehicle hasn't been claimed (rental not started)
                // If status is still 'pending', the rental was never approved/started
                $booking->update([
                    'status' => 'cancelled'
                ]);

                $count++;
                $this->line("Cancelled Booking ID: {$booking->booking_ID} (Rent Start: {$booking->rent_start})");
            }

            $this->info("Successfully cancelled {$count} expired booking(s).");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error cancelling expired bookings: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
