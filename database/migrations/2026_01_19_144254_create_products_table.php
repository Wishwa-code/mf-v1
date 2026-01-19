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
            $table->integer('minimum_loan_period');
            $table->integer('maximum_loan_period');
            $table->float('minimum_loan_amount', 10, 2);
            $table->float('maximum_loan_amount', 10, 2);
            $table->string('interest_apply_type', 45);
            $table->float('minimum_interest', 5, 2);
            $table->float('maximum_interest', 5, 2);
            $table->integer('guarantee_count');
            $table->string('collection_period_type', 45);
            $table->integer('minimum_collection_period');
            $table->integer('maximum_collection_period');
            $table->string('collection_date_type', 45);
            $table->string('penalty_method', 45);
            $table->string('penalty_apply_type', 45);
            $table->float('penalty_percentage', 5, 2);
            $table->integer('penalty_start_after_days');
            $table->enum('status', ['active', 'inactive'])->default('active');
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
