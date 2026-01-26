<?php

use App\Models\Product;
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
        Schema::create('product_has_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_item_name', 45);
            $table->foreignIdFor(Product::class)->constrained('products');
            $table->integer('minimum_loan_period')->nullable();
            $table->integer('maximum_loan_period')->nullable();
            $table->double('minimum_loan_amount', 15, 2)->nullable();
            $table->double('maximum_loan_amount', 15, 2)->nullable();
            $table->string('interest_apply_type', 45)->nullable();
            $table->float('minimum_interest', 5, 2)->nullable();
            $table->float('maximum_interest', 5, 2)->nullable();
            $table->integer('minimum_collection_period')->nullable();
            $table->integer('maximum_collection_period')->nullable();
            $table->integer('required_guarantee_count')->nullable();
            $table->string('penalty_method', 45)->nullable();
            $table->string('penalty_apply_type', 45)->nullable();
            $table->double('penalty_percentage', 5, 2)->nullable();
            $table->integer('penalty_start_after_days')->nullable();
            $table->double('saving_amount', 15, 2)->nullable();
            $table->double('saving_interest_rate', 3, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_has_items');
    }
};
