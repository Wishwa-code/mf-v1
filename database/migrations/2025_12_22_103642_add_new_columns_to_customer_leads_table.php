 <?php

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
        Schema::table('customer_leads', function (Blueprint $table) {
            $table->integer('route_id')->nullable();

            $table->foreign('route_id')
                ->references('id_route')
                ->on('route')
                ->onDelete('set null');
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('source')->default('recovery-officer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_leads', function (Blueprint $table) {
            $table->dropColumn('district');
            $table->dropColumn('city');
            $table->dropColumn('source');
        });
    }
};
