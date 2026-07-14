<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('detalle_cotizacion', 'descripcion')) {
            Schema::table('detalle_cotizacion', function (Blueprint $table) {
                $table->text('descripcion')->nullable()->after('descripcion_tela');
            });
        }

        // If old column still exists, migrate values without requiring a rename operation.
        if (Schema::hasColumn('detalle_cotizacion', 'tipo_cortina') && Schema::hasColumn('detalle_cotizacion', 'descripcion')) {
            DB::table('detalle_cotizacion')
                ->whereNull('descripcion')
                ->whereNotNull('tipo_cortina')
                ->update(['descripcion' => DB::raw('tipo_cortina')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('detalle_cotizacion', 'descripcion')) {
            Schema::table('detalle_cotizacion', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }
    }
};
