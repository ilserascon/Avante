<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'precio_publico')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->decimal('precio_publico', 10, 2)->nullable()->after('precio');
            });
        }

        DB::table('productos')
            ->whereNull('precio_publico')
            ->whereNotNull('precio')
            ->update(['precio_publico' => DB::raw('precio')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('productos', 'precio_publico')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('precio_publico');
            });
        }
    }
};
