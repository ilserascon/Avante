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
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            // columnas para cortinero cortina
            $table->unsignedBigInteger('cortinero_id')->nullable()->after('decorador_porcentaje');
            $table->integer('cortinero_cantidad')->nullable()->after('cortinero_id');
            $table->decimal('cortinero_precio', 10, 2)->nullable()->after('cortinero_cantidad');

            // columnas para cortinero tergal
            $table->unsignedBigInteger('cortinero_tergal_id')->nullable()->after('cortinero_precio');
            $table->integer('cortinero_tergal_cantidad')->nullable()->after('cortinero_tergal_id');
            $table->decimal('cortinero_tergal_precio', 10, 2)->nullable()->after('cortinero_tergal_cantidad');

            // foreign keys
            $table->foreign('cortinero_id')->references('id')->on('insumo')->onDelete('set null');
            $table->foreign('cortinero_tergal_id')->references('id')->on('insumo')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            // Eliminar foreign keys primero
            $table->dropForeign(['cortinero_id']);
            $table->dropForeign(['cortinero_tergal_id']);

            // Eliminar columnas
            $table->dropColumn([
                'cortinero_id',
                'cortinero_cantidad',
                'cortinero_precio',
                'cortinero_tergal_id',
                'cortinero_tergal_cantidad',
                'cortinero_tergal_precio',
                'tipo_cortina'
            ]);
        });
    }
};
