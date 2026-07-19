<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_insumo', function (Blueprint $table) {
            $table->decimal('descuento', 8, 2)->default(0)->after('precio_unitario');
        });

        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->decimal('descuento', 8, 2)->default(0)->after('precio_unitario');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_insumo', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });

        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });
    }
};
