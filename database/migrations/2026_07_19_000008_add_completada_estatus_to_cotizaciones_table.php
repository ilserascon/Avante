<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cotizaciones MODIFY estatus ENUM('solicitada', 'aceptada', 'rechazada', 'completada') NOT NULL DEFAULT 'solicitada'");
    }

    public function down(): void
    {
        DB::table('cotizaciones')
            ->where('estatus', 'completada')
            ->update(['estatus' => 'aceptada']);

        DB::statement("ALTER TABLE cotizaciones MODIFY estatus ENUM('solicitada', 'aceptada', 'rechazada') NOT NULL DEFAULT 'solicitada'");
    }
};
