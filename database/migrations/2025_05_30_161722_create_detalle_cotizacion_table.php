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
            $table->string('ancho_tela')->nullable();
            $table->string('ancho')->nullable();
            $table->string('largo')->nullable();
            $table->double('no_lienzos')->nullable();
            $table->integer('no_lienzos_redondeado')->nullable();
            $table->string('bastilla')->nullable();
            $table->foreignId('tela_id')->constrained('insumo');
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
