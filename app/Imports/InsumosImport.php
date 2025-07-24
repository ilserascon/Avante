<?php

namespace App\Imports;

use App\Models\Insumo;
use App\Models\Proveedor;
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

        $proveedor = Proveedor::where('nombre', $row['proveedor'] ?? '')->first();
        if (!$proveedor) {
            throw new \Exception($row['proveedor']);
        }


        $data = [
            'nombre'         => $row['nombre'] ?? null,
            'id_tipo_insumo' => $this->tipoInsumoId,
            'id_proveedor'   => $proveedor?->id,
            'costo'          => $row['costo'] ?? null,
            'precio_publico' => $row['precio_publico'] ?? null,
            'utilidad'       => $row['utilidad'] ?? null,
        ];

        // Campos dinámicos hasta campo15
        for ($i = 1; $i <= 15; $i++) {
            $col = 'campo' . $i;
            $data[$col] = $row[$col] ?? null;
        }

        // Crear insumo
        Insumo::create($data);
    }
}

