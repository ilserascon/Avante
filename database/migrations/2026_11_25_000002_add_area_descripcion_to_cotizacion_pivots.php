<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_insumo', function (Blueprint $table) {
            $table->string('area_descripcion')->nullable()->after('cantidad');
        });

        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->string('area_descripcion')->nullable()->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_insumo', function (Blueprint $table) {
            $table->dropColumn('area_descripcion');
        });

        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->dropColumn('area_descripcion');
        });
    }
};