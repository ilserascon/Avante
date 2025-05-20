<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_insumo', function (Blueprint $table) {
            $table->string('campo9')->nullable();
            $table->string('campo10')->nullable();
            $table->string('campo11')->nullable();
            $table->string('campo12')->nullable();
            $table->string('campo13')->nullable();
            $table->string('campo14')->nullable();
            $table->string('campo15')->nullable();
        });

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Telas'],  // Condición para actualizar si ya existe
            [
                'campo1' => 'Diseño',
                'campo2' => 'Ancho',
                'campo3' => 'Unidad de Medida',
                'campo4' => 'Composición',
                'campo5' => 'Libro',
                'campo6' => 'Precio CO',
                'campo7' => 'Precio RO',
                'campo8' => 'Precio Volumen',
                'campo9' => 'Modelo',
                'campo10' => 'Peso',
                'campo11' => 'Uso',
                'campo12' => 'Precio Distribuidor',
                'campo13' => 'Precio',
                'created_at' => now(),
            ]
        );
        
    }

    public function down(): void
    {
        Schema::table('tipo_insumo', function (Blueprint $table) {
            $table->dropColumn([
                'campo9', 'campo10', 'campo11', 'campo12',
                'campo13', 'campo14', 'campo15'
            ]);
        });
    }
};

