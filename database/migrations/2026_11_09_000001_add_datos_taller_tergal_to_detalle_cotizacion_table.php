<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            if (! Schema::hasColumn('detalle_cotizacion', 'confeccion_tergal')) {
                $table->string('confeccion_tergal', 255)->nullable();
            }
            if (! Schema::hasColumn('detalle_cotizacion', 'corredera_tergal_id')) {
                $table->unsignedBigInteger('corredera_tergal_id')->nullable();
            }
            if (! Schema::hasColumn('detalle_cotizacion', 'cinta_tergal_id')) {
                $table->unsignedBigInteger('cinta_tergal_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            foreach (['confeccion_tergal', 'corredera_tergal_id', 'cinta_tergal_id'] as $columna) {
                if (Schema::hasColumn('detalle_cotizacion', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
