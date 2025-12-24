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
        Schema::table('customer_leads', function (Blueprint $table) {
            $table->integer('recovery_officer_id')->nullable()->after('route_id');
            // Assuming the users table is named 'user' based on the User model
            $table->foreign('recovery_officer_id')->references('id')->on('user')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_leads', function (Blueprint $table) {
            $table->dropForeign(['recovery_officer_id']);
            $table->dropColumn('recovery_officer_id');
        });
    }
};
