<?php

namespace App\Imports;

use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\TipoProducto;
use Illuminate\Support\Facades\Log;

class ProductosImport
{
    protected int $tipoProductoId;

    protected bool $importarPrecioInterno;

    protected array $mapeoEtiquetasCampos = [];

    protected int $creados = 0;

    protected int $omitidos = 0;

    public function __construct(int $tipoProductoId, bool $importarPrecioInterno = true)
    {
        $this->tipoProductoId = $tipoProductoId;
        $this->importarPrecioInterno = $importarPrecioInterno;
    }

    public function getResumen(): array
    {
        return [
            'creados'  => $this->creados,
            'omitidos' => $this->omitidos,
        ];
    }

    public function procesarFilas(array $filas): void
    {
        $this->cargarMapeoCamposTipo();

        $filas = array_values(array_filter($filas, fn (array $row) => $this->filaTieneDatos($row)));

        if (count($filas) < 2) {
            throw new \RuntimeException(
                'El archivo no contiene filas de datos. Guarde el archivo como Excel (.xlsx) o CSV con encabezados en la primera fila y al menos una fila con información.'
            );
        }

        $encabezados = array_values($filas[0]);
        $datos = array_slice($filas, 1);

        foreach ($datos as $fila) {
            $this->procesarFila($encabezados, array_values($fila));
        }

        if ($this->creados === 0) {
            throw new \RuntimeException(
                'No se importó ningún producto. Verifique que el archivo tenga las columnas nombre y proveedor, y que los encabezados coincidan con el formato esperado.'
            );
        }
    }

    private function procesarFila(array $encabezados, array $fila): void
    {
        $row = $this->combinarEncabezadosConFila($encabezados, $fila);

        $nombre = $this->valorTexto($row, ['nombre']);
        if ($nombre === null) {
            $this->omitidos++;
            Log::warning('Fila ignorada por nombre vacío del producto.', $row);

            return;
        }

        $nombreProveedor = $this->valorTexto($row, ['proveedor']);
        if ($nombreProveedor === null) {
            $this->omitidos++;
            Log::warning('Fila ignorada por proveedor vacío.', $row);

            return;
        }

        $proveedor = $this->obtenerOCrearProveedor($nombreProveedor);

        $data = [
            'clave'            => $this->valorTexto($row, ['clave']),
            'nombre'           => $nombre,
            'color'            => $this->valorTexto($row, ['color']),
            'descripcion'      => $this->valorTexto($row, ['descripcion']),
            'precio_publico'   => $this->valorNumerico($row, ['precio_publico']),
            'id_tipo_producto' => $this->tipoProductoId,
            'id_proveedor'     => $proveedor->id,
        ];

        $data['precio'] = $this->importarPrecioInterno ? $this->valorNumerico($row, ['precio']) : null;

        for ($i = 1; $i <= TipoProducto::CAMPOS_DINAMICOS; $i++) {
            $campo = 'campo' . $i;
            $data[$campo] = $this->valorTexto($row, [$campo]);
        }

        Producto::create($data);
        $this->creados++;
    }

    private function cargarMapeoCamposTipo(): void
    {
        $tipo = TipoProducto::find($this->tipoProductoId);
        if (! $tipo) {
            return;
        }

        foreach ($tipo->camposPersonalizados() as $campo => $etiqueta) {
            $this->mapeoEtiquetasCampos[$this->normalizarClaveEncabezado($etiqueta)] = $campo;
        }
    }

    private function combinarEncabezadosConFila(array $encabezados, array $fila): array
    {
        $row = [];
        $encabezados = array_values($encabezados);
        $fila = array_values($fila);

        foreach ($encabezados as $indice => $encabezado) {
            $encabezadoTexto = Insumo::normalizarCampoMostrar($encabezado);
            if ($encabezadoTexto === '') {
                continue;
            }

            $clave = $this->normalizarClaveEncabezado($encabezadoTexto);
            $clave = $this->mapearEncabezado($clave);
            $clave = $this->mapeoEtiquetasCampos[$clave] ?? $clave;
            $row[$clave] = $fila[$indice] ?? null;
        }

        return $row;
    }

    private function filaTieneDatos(array $row): bool
    {
        foreach ($row as $valor) {
            if (Insumo::normalizarCampoMostrar($valor) !== '') {
                return true;
            }
        }

        return false;
    }

    private function normalizarClaveEncabezado($clave): string
    {
        $key = strtolower(trim((string) $clave));
        $key = str_replace(['-', ' '], '_', $key);

        return preg_replace('/_+/', '_', $key) ?? $key;
    }

    private function mapearEncabezado(string $clave): string
    {
        if (preg_match('/^precio_p.*blico$/', $clave)) {
            return 'precio_publico';
        }

        if (preg_match('/^campo(\d{1,2})$/', $clave, $coincidencias)) {
            return 'campo' . (int) $coincidencias[1];
        }

        return $clave;
    }

    private function valorTexto(array $row, array $claves): ?string
    {
        foreach ($claves as $clave) {
            if (! array_key_exists($clave, $row)) {
                continue;
            }

            $texto = Insumo::normalizarCampoMostrar($row[$clave]);
            if ($texto !== '') {
                return $texto;
            }
        }

        return null;
    }

    private function valorNumerico(array $row, array $claves): ?float
    {
        $texto = $this->valorTexto($row, $claves);
        if ($texto === null) {
            return null;
        }

        $texto = str_replace(['$', ' '], '', $texto);
        $texto = str_replace(',', '.', $texto);

        if (! is_numeric($texto)) {
            return null;
        }

        return (float) $texto;
    }

    private function obtenerOCrearProveedor(string $nombreProveedor): Proveedor
    {
        $nombreNormalizado = trim($nombreProveedor);

        $proveedor = Proveedor::query()
            ->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($nombreNormalizado)])
            ->first();

        if ($proveedor) {
            return $proveedor;
        }

        return Proveedor::create([
            'nombre'       => $nombreNormalizado,
            'rfc'          => $this->generarRfcTemporal(),
            'razon_social' => 'No especificada',
            'borrado'      => 0,
        ]);
    }

    private function generarRfcTemporal(): string
    {
        $base = 'TEMP-RFC-';

        for ($i = 1; $i <= 999; $i++) {
            $rfc = $base . str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            if (! Proveedor::where('rfc', $rfc)->exists()) {
                return $rfc;
            }
        }

        throw new \RuntimeException('Se alcanzó el límite de RFCs temporales disponibles.');
    }
}
