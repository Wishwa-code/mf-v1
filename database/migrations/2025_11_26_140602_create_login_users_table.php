<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('login_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');   // FK to tenants.id
            $table->string('email')->unique();         // Login email (unique in whole system)
            $table->boolean('active')->default(true);  // Optional
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')->on('tenants')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_users');
    }
};
