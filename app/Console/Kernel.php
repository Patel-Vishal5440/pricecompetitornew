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
        // ------------------------------------------------------------------
        // Queue worker (auto-run)
        // ------------------------------------------------------------------
        // Many features in this app dispatch long-running work to the database queue
        // (imports, pricing sync). If no queue worker is running, jobs will stay
        // "Queued" forever. This scheduled worker processes pending jobs in a
        // cron-friendly way: it exits once the queue is empty or after max-time.
        //
        // IMPORTANT: This requires the server cron to run `php artisan schedule:run`
        // every minute.
        $schedule->command('queue:work database --stop-when-empty --max-time=55 --sleep=1 --tries=3')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Load active cron jobs from database
        // Guard this so a missing table/migration doesn't break the scheduler boot.
        try {
            $cronJobs = CronJob::active()->get();
        } catch (\Throwable $e) {
            $cronJobs = collect();
            Log::warning('CronJob schedule load failed; skipping DB-driven cron jobs', [
                'error' => $e->getMessage(),
            ]);
        }
        
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
