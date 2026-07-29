<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_producto', function (Blueprint $table) {
            for ($i = 1; $i <= 10; $i++) {
                $table->string('campo' . $i)->nullable()->after($i === 1 ? 'descripcion' : 'campo' . ($i - 1));
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            for ($i = 1; $i <= 10; $i++) {
                $table->string('campo' . $i)->nullable()->after($i === 1 ? 'descripcion' : 'campo' . ($i - 1));
            }
        });
    }

    public function down(): void
    {
        Schema::table('tipo_producto', function (Blueprint $table) {
            for ($i = 1; $i <= 10; $i++) {
                $table->dropColumn('campo' . $i);
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            for ($i = 1; $i <= 10; $i++) {
                $table->dropColumn('campo' . $i);
            }
        });
    }
};
