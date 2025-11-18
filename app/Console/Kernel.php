<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\CronJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Load active cron jobs from database
        $cronJobs = CronJob::active()->get();
        
        foreach ($cronJobs as $cronJob) {
            // Get schedule times (prefer schedule_times array, fallback to single schedule_time)
            $scheduleTimes = $cronJob->schedule_times_array;
            $timezone = $cronJob->timezone ?? 'UTC';
            
            if (empty($scheduleTimes)) {
                continue; // Skip if no schedule times
            }
            
            // Schedule the command for each time
            foreach ($scheduleTimes as $time) {
                // Parse time (HH:MM format)
                list($hour, $minute) = explode(':', $time);
                
                // Store cron job ID to refresh model in callbacks
                $cronJobId = $cronJob->id;
                
                // Create a scheduled command
                $scheduledCommand = $schedule->command($cronJob->command)
                    ->timezone($timezone)
                    ->dailyAt($time)
                    ->withoutOverlapping()
                    ->onSuccess(function () use ($cronJobId) {
                        // Refresh the model to get latest data
                        $cronJob = CronJob::find($cronJobId);
                        if ($cronJob) {
                            $cronJob->update([
                                'last_run' => now(),
                                'next_run' => $cronJob->calculateNextRun(),
                                'updated_at' => now()
                            ]);
                            Log::info("Cron job '{$cronJob->name}' executed successfully at " . now());
                        }
                    })
                    ->onFailure(function () use ($cronJobId) {
                        $cronJob = CronJob::find($cronJobId);
                        if ($cronJob) {
                            Log::error("Cron job '{$cronJob->name}' failed to execute at " . now());
                        }
                    });
            }
        }
    }
    

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
