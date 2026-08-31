<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Archivo vacio con los encabezados que espera el importador de un tipo de producto o insumo.
 */
class PlantillaImportacionExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    /**
     * @param list<string> $encabezados
     */
    public function __construct(private array $encabezados, private string $titulo = 'Plantilla')
    {
    }

    /**
     * @param list<string>          $columnasBase
     * @param array<string, string> $camposPersonalizados campoN => etiqueta
     */
    public static function paraTipo(array $columnasBase, array $camposPersonalizados, string $titulo): self
    {
        $encabezados = $columnasBase;
        $usados = array_map([self::class, 'normalizar'], $columnasBase);

        foreach ($camposPersonalizados as $campo => $etiqueta) {
            // Si la etiqueta choca con otra columna se usa el nombre interno (campoN), que el importador tambien acepta.
            $encabezado = in_array(self::normalizar($etiqueta), $usados, true) ? $campo : $etiqueta;
            $usados[] = self::normalizar($encabezado);
            $encabezados[] = $encabezado;
        }

        return new self($encabezados, $titulo);
    }

    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return $this->encabezados;
    }

    public function title(): string
    {
        // Excel no admite : \ / ? * [ ] en el nombre de la hoja y la limita a 31 caracteres.
        $titulo = preg_replace('/[:\\\\\/?*\[\]]/', ' ', $this->titulo) ?? '';
        $titulo = trim(preg_replace('/\s+/', ' ', $titulo) ?? '');

        return mb_substr($titulo === '' ? 'Plantilla' : $titulo, 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /** Misma normalizacion que usan los importadores para comparar encabezados. */
    private static function normalizar(string $valor): string
    {
        $clave = strtolower(trim($valor));
        $clave = str_replace(['-', ' '], '_', $clave);

        return preg_replace('/_+/', '_', $clave) ?? $clave;
    }
}
