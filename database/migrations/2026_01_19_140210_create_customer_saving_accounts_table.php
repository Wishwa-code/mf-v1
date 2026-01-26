<?php

use App\Models\Customer;
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
        Schema::create('customer_saving_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_no');
            $table->date('open_date');
            $table->decimal('balance', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->string('interest_type', 45)->nullable();
            $table->enum('account_status', ['active', 'inactive'])->default('active');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreignIdFor(Customer::class)->constrained();    

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_saving_accounts');
    }
};
