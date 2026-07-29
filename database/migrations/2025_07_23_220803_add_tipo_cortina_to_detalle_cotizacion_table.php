<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('detalle_cotizacion', function ($table) {
            $table->string('tipo_cortina')->nullable()->after('descripcion_tela');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('detalle_cotizacion', 'tipo_cortina')) {
            Schema::table('detalle_cotizacion', function ($table) {
                $table->dropColumn('tipo_cortina');
            });
        }
    }
};
