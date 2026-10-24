<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('detalle_cotizacion', 'techo_muro')) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->string('techo_muro', 20)->nullable();
            $table->decimal('altura_izq', 10, 3)->nullable();
            $table->decimal('altura_centro', 10, 3)->nullable();
            $table->decimal('altura_der', 10, 3)->nullable();
            $table->unsignedInteger('num_bastones')->nullable();
            $table->decimal('medida_baston', 10, 3)->nullable();

            $table->decimal('ancho_fabricacion', 10, 3)->nullable();
            $table->decimal('largo_fabricacion', 10, 3)->nullable();
            $table->string('cinta_onda', 100)->nullable();
            $table->string('apertura', 20)->nullable();
            $table->string('control', 100)->nullable();

            $table->decimal('ancho_fabricacion_tergal', 10, 3)->nullable();
            $table->decimal('largo_fabricacion_tergal', 10, 3)->nullable();
            $table->string('cinta_onda_tergal', 100)->nullable();
            $table->string('apertura_tergal', 20)->nullable();
            $table->string('control_tergal', 100)->nullable();
        });
    }

    public function down(): void
    {
        $columnas = [
            'techo_muro',
            'altura_izq',
            'altura_centro',
            'altura_der',
            'num_bastones',
            'medida_baston',
            'ancho_fabricacion',
            'largo_fabricacion',
            'cinta_onda',
            'apertura',
            'control',
            'ancho_fabricacion_tergal',
            'largo_fabricacion_tergal',
            'cinta_onda_tergal',
            'apertura_tergal',
            'control_tergal',
        ];

        $existentes = array_values(array_filter($columnas, fn ($columna) => Schema::hasColumn('detalle_cotizacion', $columna)));

        if ($existentes === []) {
            return;
        }

        Schema::table('detalle_cotizacion', function (Blueprint $table) use ($existentes) {
            $table->dropColumn($existentes);
        });
    }
};
