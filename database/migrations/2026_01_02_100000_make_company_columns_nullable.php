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
        Schema::table('company', function (Blueprint $table) {
            $table->string('company_name', 255)->nullable()->change();
            $table->string('address', 255)->nullable()->change();
            $table->string('contact_no', 45)->nullable()->change();
            $table->string('logo', 255)->nullable()->change();

            $table->string('customer_num_type', 45)->nullable()->change();
            $table->string('customer_seperate_from', 45)->nullable()->change();
            $table->string('customer_num_start_from', 45)->nullable()->change();
            $table->string('customer_format', 255)->nullable()->change();

            $table->string('loan_num_type', 45)->nullable()->change();
            $table->string('loan_seperate_from', 45)->nullable()->change();
            $table->string('loan_format', 255)->nullable()->change();

            $table->string('inv_loan_num_type', 45)->nullable()->change();
            $table->string('inv_loan_seperate_from', 45)->nullable()->change();
            $table->string('inv_loan_format', 255)->nullable()->change();

            $table->string('account_saving_type', 45)->nullable()->change();
            $table->string('saving_seperate_from', 45)->nullable()->change();
            $table->string('saving_format', 255)->nullable()->change();

            $table->string('mask', 45)->nullable()->change();
            $table->string('branch', 45)->nullable()->change();

            $table->string('saturday_sunday', 45)->nullable()->change();
            $table->string('points', 45)->nullable()->change();
            $table->string('points_percentage', 45)->nullable()->change();

            $table->string('company_header', 255)->nullable()->change();
            $table->string('company_footer', 255)->nullable()->change();

            $table->integer('product_editable')->nullable()->change();
            $table->integer('banner_status')->nullable()->change();
            $table->text('banner')->nullable()->change();
            $table->integer('branch_id')->nullable()->change();

            $table->string('provider', 50)->nullable()->change();
            $table->string('customer_format_scope', 45)->nullable()->change();
            $table->string('inv_customer_number', 45)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting would require knowing the exact previous state (nullability and defaults)
        // For now, we leave this empty as is common in structural changes of this magnitude without a snapshot
    }
};
