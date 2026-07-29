<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insumo', function (Blueprint $table) {
            $table->string('clave')->nullable()->after('nombre');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('clave')->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('insumo', function (Blueprint $table) {
            $table->dropColumn('clave');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('clave');
        });
    }
};
