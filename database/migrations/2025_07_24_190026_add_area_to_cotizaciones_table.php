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
        if (!Schema::hasColumn('detalle_cotizacion', 'area')) {
            Schema::table('detalle_cotizacion', function (Blueprint $table) {
                if (Schema::hasColumn('detalle_cotizacion', 'descripcion')) {
                    $table->string('area')->nullable()->after('descripcion');
                    return;
                }

                $table->string('area')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('detalle_cotizacion', 'area')) {
            Schema::table('detalle_cotizacion', function (Blueprint $table) {
                $table->dropColumn('area');
            });
        }
    }
};
