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
        // Update customer_leads table coordinates
        Schema::table('customer_leads', function (Blueprint $table) {
            // Change latitude and longitude to decimal with high precision (18 total digits, 15 after decimal)
            // This allows coordinates like: 6.965877610455267, 79.92680611557401
            $table->decimal('latitude', 18, 15)->nullable()->change();
            $table->decimal('longitude', 18, 15)->nullable()->change();
            $table->decimal('visited_latitude', 18, 15)->nullable()->change();
            $table->decimal('visited_longitude', 18, 15)->nullable()->change();
        });

        // Update lead_has_images table coordinates
        Schema::table('lead_has_images', function (Blueprint $table) {
            $table->decimal('latitude', 18, 15)->nullable()->change();
            $table->decimal('longitude', 18, 15)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert customer_leads table
        Schema::table('customer_leads', function (Blueprint $table) {
            // Revert back to float(10, 6)
            $table->float('latitude', 10, 6)->nullable()->change();
            $table->float('longitude', 10, 6)->nullable()->change();
            $table->float('visited_latitude', 10, 6)->nullable()->change();
            $table->float('visited_longitude', 10, 6)->nullable()->change();
        });

        // Revert lead_has_images table
        Schema::table('lead_has_images', function (Blueprint $table) {
            $table->float('latitude', 10, 6)->nullable()->change();
            $table->float('longitude', 10, 6)->nullable()->change();
        });
    }
};
