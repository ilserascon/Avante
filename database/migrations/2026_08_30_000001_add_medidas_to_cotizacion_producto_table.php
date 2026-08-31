<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->decimal('ancho', 10, 2)->nullable()->after('producto_id');
            $table->decimal('largo', 10, 2)->nullable()->after('ancho');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion_producto', function (Blueprint $table) {
            $table->dropColumn(['ancho', 'largo']);
        });
    }
};
