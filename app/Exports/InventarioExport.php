<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    /**
     * @param list<array{tipo: string, nombre: string, cantidad_total: float, almacenes: list<array{nombre: string, cantidad: float}>}> $items
     */
    public function __construct(private array $items)
    {
    }

    public function headings(): array
    {
        return ['Tipo', 'Nombre', 'Cantidad total', 'Almacenes'];
    }

    public function array(): array
    {
        return array_map(function (array $item) {
            $almacenes = collect($item['almacenes'])
                ->map(function (array $almacen) {
                    return $almacen['nombre'] . ' (' . $this->formatearCantidad($almacen['cantidad']) . ')';
                })
                ->implode('; ');

            return [
                $item['tipo'],
                $item['nombre'],
                $this->formatearCantidad($item['cantidad_total']),
                $almacenes,
            ];
        }, $this->items);
    }

    public function title(): string
    {
        return 'Inventario';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function formatearCantidad(float $cantidad): string
    {
        $formateado = number_format($cantidad, 2, '.', '');

        return rtrim(rtrim($formateado, '0'), '.');
    }
}
