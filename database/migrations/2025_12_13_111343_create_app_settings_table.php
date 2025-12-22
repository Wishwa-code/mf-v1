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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->foreign('created_by')
                ->references('id')
                ->on('user')
                ->nullOnDelete();

            $table->foreign('updated_by')
                ->references('id')
                ->on('user')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
