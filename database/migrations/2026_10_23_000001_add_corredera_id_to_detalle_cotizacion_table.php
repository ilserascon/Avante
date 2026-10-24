<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('detalle_cotizacion', 'corredera_id')) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->unsignedBigInteger('corredera_id')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('detalle_cotizacion', 'corredera_id')) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->dropColumn('corredera_id');
        });
    }
};
