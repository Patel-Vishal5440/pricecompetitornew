<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->json('schedule_times')->nullable()->after('schedule_time')->comment('Multiple schedule times in JSON format: ["12:00", "15:00", "18:00", "21:00"]');
            $table->string('timezone', 50)->default('UTC')->after('schedule_times')->comment('Timezone for schedule times (default: UTC)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cron_jobs', function (Blueprint $table) {
            $table->dropColumn(['schedule_times', 'timezone']);
        });
    }
};
