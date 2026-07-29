<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insumo', function (Blueprint $table) {
            if (!Schema::hasColumn('insumo', 'color')) {
                $table->string('color')->nullable()->after('clave');
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'color')) {
                $table->string('color')->nullable()->after('clave');
            }
        });
    }

    public function down(): void
    {
        Schema::table('insumo', function (Blueprint $table) {
            if (Schema::hasColumn('insumo', 'color')) {
                $table->dropColumn('color');
            }
        });

        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
