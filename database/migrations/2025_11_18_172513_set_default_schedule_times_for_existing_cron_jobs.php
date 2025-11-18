<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Set default schedule times (12:00, 15:00, 18:00, 21:00 UTC) for existing cron jobs
     * that don't have schedule_times set.
     *
     * @return void
     */
    public function up()
    {
        // Default schedule times: 12:00 PM, 3:00 PM, 6:00 PM, 9:00 PM (UTC)
        $defaultScheduleTimes = json_encode(['12:00', '15:00', '18:00', '21:00']);
        $defaultTimezone = 'UTC';

        // Update cron jobs that don't have schedule_times set
        DB::table('cron_jobs')
            ->where(function ($query) {
                $query->whereNull('schedule_times')
                      ->orWhere('schedule_times', '[]')
                      ->orWhere('schedule_times', '')
                      ->orWhere('schedule_times', 'null');
            })
            ->update([
                'schedule_times' => $defaultScheduleTimes,
                'timezone' => $defaultTimezone,
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally clear schedule_times when rolling back
        // DB::table('cron_jobs')->update(['schedule_times' => null]);
    }
};
