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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('customer_code');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('contact_no')->unique()->nullable();
            $table->integer('new_nic')->unique()->nullable();
            $table->string('old_nic')->unique()->nullable();

            $table->unsignedBigInteger('acc_center_cus_id');
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('route_id')->references('id_route')->on('routes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
