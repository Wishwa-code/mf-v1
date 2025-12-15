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
        Schema::create('customer_leads', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->float('longitude', 10, 6)->nullable();
            $table->float('latitude', 10, 6)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->enum('type',['group','individual','business','leasing'])->default('group');
            $table->enum('status',['pending','pending-approved','agreement-signed','loan-issued'])->default('pending');
            $table->dateTime('created_at_lead')->nullable();

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
            $table->timestamps();

            $table->boolean('is_visited')->default(false);
            $table->longText('visit_notes')->nullable();
            $table->float('visited_longitude', 10, 6)->nullable();
            $table->float('visited_latitude', 10, 6)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_leads');
    }
};
