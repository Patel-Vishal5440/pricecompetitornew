<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to modify column type to avoid Doctrine DBAL compatibility issues
        DB::statement('ALTER TABLE products MODIFY odoo_id VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Convert back to integer (this may fail if there are string values)
        DB::statement('ALTER TABLE products MODIFY odoo_id INT NOT NULL');
    }
};
