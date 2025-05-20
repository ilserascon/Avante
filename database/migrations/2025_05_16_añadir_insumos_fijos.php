<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('insumo')->insert([
            [
                'nombre' => 'Ojillos',
                'id_tipo_insumo' => 2,
                'id_proveedor' => 1,
                'costo' => 15.00,
                'precio_publico' => 15.00,
                'utilidad' => round((15.00 * 1.20 * 1.16) - 15.00, 2),
                'created_at' => now()
            ],
            [
                'nombre' => 'Cortinero',
                'id_tipo_insumo' => 2,
                'id_proveedor' => 1,
                'costo' => 200.00,
                'precio_publico' => 200.00,
                'utilidad' => round((200.00 * 1.20 * 1.16) - 200.00, 2),
                'created_at' => now()
            ],
            [
                'nombre' => 'Puntas',
                'id_tipo_insumo' => 2,
                'id_proveedor' => 1,
                'costo' => 250.00,
                'precio_publico' => 250.00,
                'utilidad' => round((250.00 * 1.20 * 1.16) - 250.00, 2),
                'created_at' => now()
            ],
            [
                'nombre' => 'Mensulas',
                'id_tipo_insumo' => 2,
                'id_proveedor' => 1,
                'costo' => 120.00,
                'precio_publico' => 120.00,
                'utilidad' => round((120.00 * 1.20 * 1.16) - 120.00, 2),
                'created_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('insumo')->whereIn('nombre', [
            'Ojillos', 'Cortinero', 'Puntas', 'Mensulas'
        ])->delete();
    }
};