<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the old role column
            $table->dropColumn('role');
            
            // Add the new role_id foreign key
            $table->foreignId('role_id')->nullable()->after('email')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            
            // Add back the old role column
            $table->string('role')->nullable()->after('email');
        });
    }
};
