<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipo_insumo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('campo1')->nullable();
            $table->string('campo2')->nullable();
            $table->string('campo3')->nullable();
            $table->string('campo4')->nullable();
            $table->string('campo5')->nullable();
            $table->string('campo6')->nullable();
            $table->string('campo7')->nullable();
            $table->string('campo8')->nullable();
            $table->string('campo9')->nullable();
            $table->string('campo10')->nullable();
            $table->string('campo11')->nullable();
            $table->string('campo12')->nullable();
            $table->string('campo13')->nullable();
            $table->string('campo14')->nullable();
            $table->string('campo15')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Telas'],
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

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Accesorios'],
            [
                'created_at' => now(),
            ]
        );

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Mano de Obra'],
            [
                'created_at' => now(),
            ]
        );

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Tergal'],
            [
                'Campo1' => 'Ancho',
                'Campo2' => 'Largo',
                'created_at' => now(),
            ]
        );

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Forro'],
            [
                'Campo1' => 'Ancho',
                'Campo2' => 'Largo',
                'created_at' => now(),
            ]
        );

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Cortinero'],
            [
                'campo1' => 'Modelo',
                'campo2' => 'Medida Minima (Mts)',
                'campo3' => 'Medida Maxima (Mts)',
                'campo4' => '1 Jalon (Izquierda o Derecha)',
                'campo5' => '2 Jalones (Centro o Lateral)',
                'campo6' => 'Con Control',
                'campo7' => 'Sin Control',
                'campo8' => 'Riplefold',
                'campo9' => 'Tradicional',
                'created_at' => now(),
            ]
        );

        DB::table('tipo_insumo')->updateOrInsert(
            ['nombre' => 'Baston'],
            [
                'Campo1' => 'Medida',
                'Campo2' => 'Precio Deco',
                'Campo3' => 'Proveedor',
                'created_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_insumo');
    }
};
