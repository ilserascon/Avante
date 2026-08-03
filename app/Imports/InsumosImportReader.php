<?php

namespace App\Imports;

use App\Models\Insumo;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InsumosImportReader
{
    public static function leerArchivo(UploadedFile|string $archivo): array
    {
        $ruta = $archivo instanceof UploadedFile ? $archivo->getRealPath() : $archivo;
        $extension = strtolower(
            $archivo instanceof UploadedFile
                ? $archivo->getClientOriginalExtension()
                : pathinfo((string) $archivo, PATHINFO_EXTENSION)
        );

        if (in_array($extension, ['csv', 'txt'], true)) {
            $filas = self::leerCsv($ruta);
            if (count($filas) >= 2) {
                return $filas;
            }
        }

        $spreadsheet = IOFactory::load($ruta);
        $mejorHoja = [];

        foreach ($spreadsheet->getAllSheets() as $hoja) {
            $filas = self::leerHoja($hoja);
            if (count($filas) > count($mejorHoja)) {
                $mejorHoja = $filas;
            }
        }

        return $mejorHoja;
    }

    private static function leerCsv(string $ruta): array
    {
        $mejorLectura = [];

        foreach ([',', ';', "\t"] as $delimitador) {
            $reader = new Csv();
            $reader->setDelimiter($delimitador);
            $reader->setEnclosure('"');
            $reader->setInputEncoding('UTF-8');

            try {
                $spreadsheet = $reader->load($ruta);
                $filas = self::leerHoja($spreadsheet->getActiveSheet());
            } catch (\Throwable) {
                continue;
            }

            if (count($filas) > count($mejorLectura) && self::pareceTablaValida($filas)) {
                $mejorLectura = $filas;
            }
        }

        return $mejorLectura;
    }

    private static function leerHoja(Worksheet $hoja): array
    {
        $filaMaxima = $hoja->getHighestDataRow();
        $columnaMaxima = $hoja->getHighestDataColumn();

        if ($filaMaxima < 1 || $columnaMaxima === '') {
            return [];
        }

        $indiceColumnaMaxima = Coordinate::columnIndexFromString($columnaMaxima);
        $filas = [];

        for ($fila = 1; $fila <= $filaMaxima; $fila++) {
            $datosFila = [];

            for ($columna = 1; $columna <= $indiceColumnaMaxima; $columna++) {
                $datosFila[] = $hoja->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
            }

            if (self::filaTieneDatos($datosFila)) {
                $filas[] = $datosFila;
            }
        }

        return $filas;
    }

    private static function pareceTablaValida(array $filas): bool
    {
        if (count($filas) < 2) {
            return false;
        }

        $encabezados = self::normalizarEncabezados($filas[0]);

        return in_array('nombre', $encabezados, true) || in_array('proveedor', $encabezados, true);
    }

    private static function normalizarEncabezados(array $fila): array
    {
        return array_values(array_filter(array_map(function ($valor) {
            $texto = Insumo::normalizarCampoMostrar($valor);
            if ($texto === '') {
                return null;
            }

            $clave = strtolower(trim($texto));
            $clave = str_replace(['-', ' '], '_', $clave);

            return preg_replace('/_+/', '_', $clave) ?? $clave;
        }, $fila)));
    }

    private static function filaTieneDatos(array $fila): bool
    {
        foreach ($fila as $valor) {
            if (Insumo::normalizarCampoMostrar($valor) !== '') {
                return true;
            }
        }

        return false;
    }
}
