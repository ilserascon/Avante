<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detalle_cotizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->onDelete('cascade');

            $table->foreignId('tela_id')->nullable()->constrained('insumo');
            $table->string('ancho_tela')->nullable();
            $table->string('ancho')->nullable();
            $table->string('largo')->nullable();
            $table->double('no_lienzos')->nullable();
            $table->integer('no_lienzos_redondeado')->nullable();
            $table->string('bastilla')->nullable();

            $table->foreignId('tergal_id')->nullable()->constrained('insumo');
            $table->string('ancho_tergal')->nullable();
            $table->string('ancho_tergal_real')->nullable();
            $table->string('largo_tergal')->nullable();
            $table->double('no_lienzos_tergal')->nullable();
            $table->integer('no_lienzos_redondeado_tergal')->nullable();
            $table->string('bastilla_tergal')->nullable();

            $table->foreignId('forro_id')->nullable()->constrained('insumo');
            $table->string('ancho_forro')->nullable();
            $table->string('ancho_forro_real')->nullable();
            $table->string('largo_forro')->nullable();
            $table->double('no_lienzos_forro')->nullable();
            $table->integer('no_lienzos_redondeado_forro')->nullable();
            $table->string('bastilla_forro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_cortina');
    }
};
