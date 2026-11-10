<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            if (! Schema::hasColumn('detalle_cotizacion', 'espacio')) {
                $table->decimal('espacio', 10, 3)->nullable();
            }
            if (! Schema::hasColumn('detalle_cotizacion', 'espacio_tergal')) {
                $table->decimal('espacio_tergal', 10, 3)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            foreach (['espacio', 'espacio_tergal'] as $columna) {
                if (Schema::hasColumn('detalle_cotizacion', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
