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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name', 45);
            $table->string('product_code', 45);
            $table->string('interest_method', 45);
            $table->string('loan_period_type', 45);
            $table->string('interest_period_type', 45);
            $table->string('collection_period_type', 45);
            $table->string('collection_date_type', 45);
            $table->integer('guarantee_count')->nullable();
            $table->string('saving_amount_type', 45)->nullable();
            $table->string('saving_collection_type', 45)->nullable();
            $table->string('saving_interest_cal_type', 45)->nullable();
            $table->enum('saving_account_status', ['active', 'inactive'])->default('active');
            $table->enum('recovery_account_status', ['active', 'inactive'])->default('active');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
