<?php

use App\Models\Customer;
use App\Models\Route;
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
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->string('center_name');
            $table->string('center_code');
            $table->string('contact_no');
            $table->longText('adress');
            $table->float('longitude', 4, 9);
            $table->float('latitude', 4, 9);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreignIdFor(Customer::class)->constrained();
            $table->foreignIdFor(Route::class)->constrained();
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
        Schema::dropIfExists('centers');
    }
};
