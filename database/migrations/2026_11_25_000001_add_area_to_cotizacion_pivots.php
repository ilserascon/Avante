<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_insumo', function (Blueprint $table) {
            $table->decimal('area', 10, 2)->nullable()->after('cantidad');
        });

        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->decimal('area', 10, 2)->nullable()->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_insumo', function (Blueprint $table) {
            $table->dropColumn('area');
        });

        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};