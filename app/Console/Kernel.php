<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Cancel expired pending bookings daily at 2 AM
        $schedule->command('bookings:cancel-expired')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->onFailure(function () {
                // Log or handle failure if needed
                Log::error('Failed to cancel expired bookings');
            })
            ->onSuccess(function () {
                // Log success
                Log::info('Successfully executed cancel expired bookings command');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
