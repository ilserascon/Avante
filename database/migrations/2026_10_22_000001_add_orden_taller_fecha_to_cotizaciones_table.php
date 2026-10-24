<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cotizaciones', 'orden_taller_fecha')) {
            return;
        }

        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->date('orden_taller_fecha')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('cotizaciones', 'orden_taller_fecha')) {
            return;
        }

        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn('orden_taller_fecha');
        });
    }
};
