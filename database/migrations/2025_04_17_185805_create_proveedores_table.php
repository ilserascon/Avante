<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rfc')->unique();
            $table->string('razon_social');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable()->unique();
            $table->boolean('borrado')->default(0);
            $table->timestamps();
        });

        DB::table('proveedores')->insert([
            [
                'nombre' => 'VISTATEX LEGLAND',
                'rfc' => 'TEMP-RFC-001',
                'razon_social' => 'SIN RAZÓN SOCIAL',
                'telefono' => '',
                'email' => null,
                'borrado' => 0,
                'created_at' => now(),
            ],
            [
                'nombre' => 'AMERICAN TELAS',
                'rfc' => 'TEMP-RFC-002',
                'razon_social' => 'SIN RAZÓN SOCIAL',
                'telefono' => '',
                'email' => null,
                'borrado' => 0,
                'created_at' => now(),
            ],
            [
                'nombre' => 'DICSA',
                'rfc' => 'TEMP-RFC-003',
                'razon_social' => 'SIN RAZÓN SOCIAL',
                'telefono' => '',
                'email' => null,
                'borrado' => 0,
                'created_at' => now(),
            ],
        ]);
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};