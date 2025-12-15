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
        Schema::table('lead_has_images', function (Blueprint $table) {
            $table->float('latitude', 10, 6)->nullable()->after('image_type');
            $table->float('longitude', 10, 6)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lead_has_images', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
