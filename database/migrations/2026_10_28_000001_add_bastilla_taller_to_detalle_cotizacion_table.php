<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('detalle_cotizacion', 'bastilla_taller')) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->decimal('bastilla_taller', 10, 3)->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('detalle_cotizacion', 'bastilla_taller')) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->dropColumn('bastilla_taller');
        });
    }
};
