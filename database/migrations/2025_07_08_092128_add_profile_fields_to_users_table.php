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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'country', 'city', 'company_name', 'website', 'bio', 'skills']);
        });
    }
};
