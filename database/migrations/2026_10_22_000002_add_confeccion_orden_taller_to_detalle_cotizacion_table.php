<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            if (! Schema::hasColumn('detalle_cotizacion', 'confeccion')) {
                $table->string('confeccion', 255)->nullable();
            }
            if (! Schema::hasColumn('detalle_cotizacion', 'vuelta_lateral')) {
                $table->decimal('vuelta_lateral', 10, 3)->nullable();
            }
            if (! Schema::hasColumn('detalle_cotizacion', 'observacion_taller')) {
                $table->text('observacion_taller')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columnas = array_values(array_filter(
            ['confeccion', 'vuelta_lateral', 'observacion_taller'],
            fn ($columna) => Schema::hasColumn('detalle_cotizacion', $columna)
        ));

        if ($columnas === []) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) use ($columnas) {
            $table->dropColumn($columnas);
        });
    }
};
