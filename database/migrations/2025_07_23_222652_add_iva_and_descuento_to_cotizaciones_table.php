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
        Schema::table('cotizaciones', function ($table) {
            $table->boolean('aplicar_iva')->default(0);
            $table->decimal('descuento', 5, 2)->default(0);
        });
    }
    public function down()
    {
        Schema::table('cotizaciones', function ($table) {
            $table->dropColumn(['aplicar_iva', 'descuento']);
        });
    }
};
