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
        Schema::create('route', function (Blueprint $table) {
            $table->id('id_route'); 
            $table->text('name');
            $table->string('root_code', 45);

            $table->unsignedBigInteger('id_officer');
            $table->unsignedBigInteger('branch_id')->default(1);

            $table->string('collection_type', 45)->default('customizable');
            $table->string('collection_date', 45)->default('Monday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route');
    }
};
