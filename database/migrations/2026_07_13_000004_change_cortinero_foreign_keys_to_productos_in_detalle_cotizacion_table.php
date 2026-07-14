<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->dropForeign(['cortinero_id']);
            $table->dropForeign(['cortinero_tergal_id']);

            $table->foreign('cortinero_id')
                ->references('id')
                ->on('productos')
                ->nullOnDelete();

            $table->foreign('cortinero_tergal_id')
                ->references('id')
                ->on('productos')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->dropForeign(['cortinero_id']);
            $table->dropForeign(['cortinero_tergal_id']);

            $table->foreign('cortinero_id')
                ->references('id')
                ->on('insumo')
                ->nullOnDelete();

            $table->foreign('cortinero_tergal_id')
                ->references('id')
                ->on('insumo')
                ->nullOnDelete();
        });
    }
};
