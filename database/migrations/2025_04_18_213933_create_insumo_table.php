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
        Schema::create('insumo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->foreignId('id_tipo_insumo')->constrained('tipo_insumo');
            $table->foreignId('id_proveedor')->constrained('proveedores');
            $table->decimal('costo', 10, 2)->nullable();
            $table->decimal('precio_publico', 10, 2)->nullable();
            $table->decimal('utilidad', 10, 2)->nullable();
            $table->string('campo1')->nullable();
            $table->string('campo2')->nullable();
            $table->string('campo3')->nullable();
            $table->string('campo4')->nullable();
            $table->string('campo5')->nullable();
            $table->string('campo6')->nullable();
            $table->string('campo7')->nullable();
            $table->string('campo8')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumo');
    }
};