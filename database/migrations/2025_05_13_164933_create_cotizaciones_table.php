<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));

            $table->boolean('lleva_cortina')->default(false);
            $table->boolean('lleva_tergal')->default(false);
            $table->boolean('lleva_forro')->default(false);

            $table->decimal('total_lienzos', 10, 2)->nullable();
            $table->decimal('total_m2_forro', 10, 2)->nullable();
            $table->decimal('total_m2_tela', 10, 2)->nullable();
            $table->decimal('total_m2_tergal', 10, 2)->nullable();

            $table->decimal('costo_cortina', 10, 2)->nullable();
            $table->decimal('utilidad', 10, 2)->nullable();
            $table->decimal('costo_decorador', 10, 2)->nullable();
            $table->decimal('precio_publico', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotizaciones');
    }
}
