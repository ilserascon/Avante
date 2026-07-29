<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('detalle_cotizacion', 'materiales_varios')) {
            Schema::table('detalle_cotizacion', function (Blueprint $table) {
                $table->json('materiales_varios')->nullable()->after('cortinero_tergal_precio');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('detalle_cotizacion', 'materiales_varios')) {
            Schema::table('detalle_cotizacion', function (Blueprint $table) {
                $table->dropColumn('materiales_varios');
            });
        }
    }
};
