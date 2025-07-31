<?php

namespace App\Imports;

use App\Models\Insumo;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InsumosImport implements OnEachRow, WithHeadingRow
{
    protected $tipoInsumoId;

    public function __construct($tipoInsumoId)
    {
        $this->tipoInsumoId = $tipoInsumoId;
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $nombreProveedor = trim($row['proveedor'] ?? '');

        if ($nombreProveedor === '') {
            Log::warning('Fila ignorada por proveedor vacío: ', $row);
            return;
        }

        // Buscar proveedor por nombre
        $proveedor = Proveedor::where('nombre', $nombreProveedor)->first();

        // Si no existe, crear con RFC temporal único
        if (!$proveedor) {
            $rfc = $this->generarRfcTemporal();

            $proveedor = Proveedor::create([
                'nombre' => $nombreProveedor,
                'rfc' => $rfc,
                'razon_social' => 'No especificada',
            ]);
        }

        // Validar que 'nombre' del insumo no sea null
        if (empty($row['nombre'])) {
            Log::warning('Fila ignorada por nombre vacío del insumo: ', $row);
            return;
        }

        $data = [
            'nombre'         => $row['nombre'],
            'id_tipo_insumo' => $this->tipoInsumoId,
            'id_proveedor'   => $proveedor->id,
            'costo'          => $row['costo'] ?? null,
            'precio_publico' => $row['precio_publico'] ?? null,
            'utilidad'       => $row['utilidad'] ?? null,
        ];

        // Campos dinámicos campo1 - campo15
        for ($i = 1; $i <= 15; $i++) {
            $campo = 'campo' . $i;
            $data[$campo] = $row[$campo] ?? null;
        }

        Insumo::create($data);
    }

    private function generarRfcTemporal()
    {
        $base = 'TEMP-RFC-';
        for ($i = 1; $i <= 999; $i++) {
            $rfc = $base . str_pad($i, 3, '0', STR_PAD_LEFT);
            if (!Proveedor::where('rfc', $rfc)->exists()) {
                return $rfc;
            }
        }

        throw new \Exception('Se alcanzó el límite de RFCs temporales disponibles.');
    }
}
