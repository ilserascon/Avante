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

            $table->boolean('lleva_cortina')->default(false);
            $table->boolean('lleva_tergal')->default(false);
            $table->boolean('lleva_forro')->default(false);

            $table->decimal('total_lienzos', 10, 2)->nullable();
            $table->decimal('total_m2_forro', 10, 2)->nullable();
            $table->decimal('total_m2_tela', 10, 2)->nullable();
            $table->decimal('total_m2_tergal', 10, 2)->nullable();

            $table->decimal('costo_cortina', 10, 2)->nullable();
            
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

            $table->double('total_tela')->nullable();
            $table->double('precio_m2_tela')->nullable();
            $table->text('descripcion_tela')->nullable();
            $table->double('total_tela_final')->nullable();

            $table->double('total_tergal')->nullable();
            $table->double('precio_m2_tergal')->nullable();
            $table->text('descripcion_tergal')->nullable();
            $table->double('total_tergal_final')->nullable();

            $table->double('total_forro')->nullable();
            $table->double('precio_m2_forro')->nullable();
            $table->text('descripcion_forro')->nullable();
            $table->double('total_final_forro')->nullable();

            $table->double('costo_total_tela_tergal_forro')->nullable();

            // Mano de obra
            $table->double('m2_1')->nullable();
            $table->double('costo_mano_obra_1')->nullable();
            $table->double('total_mano_obra_1')->nullable();

            $table->double('m2_2')->nullable();
            $table->double('costo_mano_obra_2')->nullable();
            $table->double('total_mano_obra_2')->nullable();

            $table->double('costo_total_mano_obra')->nullable();
            $table->double('decorador_porcentaje')->nullable();
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
