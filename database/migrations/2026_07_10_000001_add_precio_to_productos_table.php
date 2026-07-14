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
        if (!Schema::hasColumn('productos', 'precio')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->decimal('precio', 10, 2)->nullable()->after('descripcion');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('productos', 'precio')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('precio');
            });
        }
    }
};
