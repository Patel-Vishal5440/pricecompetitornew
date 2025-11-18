<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CronJob;
use App\Models\User;
use Carbon\Carbon;

class CronJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user as creator (or use admin user)
        $user = User::first();
        $userId = $user ? $user->id : null;

        // Default schedule times: 12:00, 15:00, 18:00, 21:00 (UTC)
        $defaultScheduleTimes = ['12:00', '15:00', '18:00', '21:00'];

        // Create a default cron job entry
        CronJob::create([
            'name' => 'Default Cron Job',
            'description' => 'Default cron job with standard schedule times (12:00, 15:00, 18:00, 21:00 UTC)',
            'schedule_time' => Carbon::createFromTime(12, 0, 0), // Default to 12:00 for schedule_time field
            'schedule_times' => $defaultScheduleTimes,
            'timezone' => 'UTC',
            'command' => 'php artisan schedule:run',
            'is_active' => true,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }
}

