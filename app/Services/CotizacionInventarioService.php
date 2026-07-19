<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\Existencia;
use App\Models\Insumo;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CotizacionInventarioService
{
    /**
     * Valida existencia, descuenta inventario y marca como completada. Retorna mensaje de error o null.
     */
    public function procesarCompletado(Cotizacion $cotizacion): ?string
    {
        try {
            DB::transaction(function () use ($cotizacion) {
                $this->cargarRelaciones($cotizacion);

                $requerimientos = $this->resolverRequerimientos($cotizacion);
                $faltantes = $this->listarFaltantesDesdeRequerimientos($requerimientos);

                if (!empty($faltantes)) {
                    throw new RuntimeException($this->mensajeNoSePuedeCompletar($faltantes));
                }

                $this->descontarRequerimientos($requerimientos);
            });
        } catch (RuntimeException $e) {
            return $e->getMessage();
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function listarFaltantes(Cotizacion $cotizacion): array
    {
        $this->cargarRelaciones($cotizacion);

        return $this->listarFaltantesDesdeRequerimientos($this->resolverRequerimientos($cotizacion));
    }

    public function validarExistencia(Cotizacion $cotizacion): ?string
    {
        $faltantes = $this->listarFaltantes($cotizacion);

        return empty($faltantes) ? null : $this->mensajeNoSePuedeCompletar($faltantes);
    }

    /**
     * @param string[] $faltantes
     */
    public function mensajeNoSePuedeCompletar(array $faltantes): string
    {
        return $this->formatearListadoFaltantes(
            'No se puede completar la cotización por insumos/productos faltantes:',
            $faltantes
        );
    }

    /**
     * @param string[] $faltantes
     */
    public function mensajeAceptadaConFaltantes(array $faltantes): string
    {
        return $this->formatearListadoFaltantes(
            'Cotización aceptada. Hay insumos/productos faltantes en inventario:',
            $faltantes
        );
    }

    /**
     * @param string[] $faltantes
     */
    private function formatearListadoFaltantes(string $titulo, array $faltantes): string
    {
        $items = collect($faltantes)
            ->map(fn (string $faltante) => '<li>' . e($faltante) . '</li>')
            ->implode('');

        return '<div><strong>' . e($titulo) . '</strong><ul class="mb-0 mt-2 ps-3">' . $items . '</ul></div>';
    }

    private function cargarRelaciones(Cotizacion $cotizacion): void
    {
        $cotizacion->load([
            'insumos',
            'productos.tipoProducto',
            'productos.insumos',
            'detallesCotizacion',
            'detalleCotizacion',
        ]);
    }

    private function obtenerDetalles(Cotizacion $cotizacion): Collection
    {
        $detalles = $cotizacion->detallesCotizacion;

        if ($detalles->isEmpty() && $cotizacion->detalleCotizacion) {
            return collect([$cotizacion->detalleCotizacion]);
        }

        return $detalles;
    }

    /**
     * @return array{insumos: array<int, float>, productos: array<int, float>}
     */
    private function recolectarRequerimientos(Cotizacion $cotizacion): array
    {
        $insumos = [];
        $productos = [];
        $cortineros = [];

        foreach ($cotizacion->insumos as $insumo) {
            $this->acumularCantidad($insumos, (int) $insumo->id, (float) $insumo->pivot->cantidad);
        }

        foreach ($cotizacion->productos as $producto) {
            $cantidad = (float) $producto->pivot->cantidad;
            if ($cantidad <= 0) {
                continue;
            }

            if ($this->esCortinero($producto)) {
                $this->acumularCantidad($cortineros, (int) $producto->id, $cantidad);
            } else {
                $this->acumularCantidad($productos, (int) $producto->id, $cantidad);
            }
        }

        foreach ($this->obtenerDetalles($cotizacion) as $detalle) {
            if ($detalle->tela_id && (float) $detalle->total_tela > 0) {
                $this->acumularCantidad($insumos, (int) $detalle->tela_id, (float) $detalle->total_tela);
            }

            if ($detalle->tergal_id && (float) $detalle->total_tergal > 0) {
                $this->acumularCantidad($insumos, (int) $detalle->tergal_id, (float) $detalle->total_tergal);
            }

            if ($detalle->forro_id && (float) $detalle->total_forro > 0) {
                $this->acumularCantidad($insumos, (int) $detalle->forro_id, (float) $detalle->total_forro);
            }

            if ($detalle->cortinero_id && (float) $detalle->cortinero_cantidad > 0) {
                $this->acumularCantidad($cortineros, (int) $detalle->cortinero_id, (float) $detalle->cortinero_cantidad);
            }

            if ($detalle->cortinero_tergal_id && (float) $detalle->cortinero_tergal_cantidad > 0) {
                $this->acumularCantidad($cortineros, (int) $detalle->cortinero_tergal_id, (float) $detalle->cortinero_tergal_cantidad);
            }
        }

        return [
            'insumos' => $insumos,
            'productos' => $productos,
            'cortineros' => $cortineros,
        ];
    }

    /**
     * @param array{insumos: array<int, float>, productos: array<int, float>, cortineros: array<int, float>} $raw
     * @return array{insumos: array<int, float>, productos: array<int, float>}
     */
    private function resolverRequerimientos(Cotizacion $cotizacion): array
    {
        $raw = $this->recolectarRequerimientos($cotizacion);
        $insumos = $raw['insumos'];
        $productos = $raw['productos'];

        if (empty($raw['cortineros'])) {
            return compact('insumos', 'productos');
        }

        $productosCortinero = Producto::with(['tipoProducto', 'insumos'])
            ->whereIn('id', array_keys($raw['cortineros']))
            ->get()
            ->keyBy('id');

        foreach ($raw['cortineros'] as $productoId => $cantidadNecesaria) {
            /** @var Producto|null $producto */
            $producto = $productosCortinero->get($productoId);

            if (!$producto) {
                continue;
            }

            $stockProducto = $this->obtenerStockProducto($productoId);

            if ($stockProducto + 0.00001 >= $cantidadNecesaria) {
                $this->acumularCantidad($productos, $productoId, $cantidadNecesaria);
                continue;
            }

            if ($producto->insumos->isEmpty()) {
                $nombre = $producto->nombre;

                return [
                    'insumos' => $insumos,
                    'productos' => $productos,
                    '_error' => "No hay existencia suficiente del cortinero \"{$nombre}\" (se requieren {$this->formatearCantidad($cantidadNecesaria)}, hay {$this->formatearCantidad($stockProducto)}) y no tiene insumos configurados para fabricarlo.",
                ];
            }

            foreach ($producto->insumos as $insumo) {
                $cantidadInsumo = $cantidadNecesaria * (float) ($insumo->pivot->cantidad ?? 0);
                if ($cantidadInsumo > 0) {
                    $this->acumularCantidad($insumos, (int) $insumo->id, $cantidadInsumo);
                }
            }
        }

        return compact('insumos', 'productos');
    }

    /**
     * @param array{insumos: array<int, float>, productos: array<int, float>, _error?: string} $requerimientos
     * @return string[]
     */
    private function listarFaltantesDesdeRequerimientos(array $requerimientos): array
    {
        if (!empty($requerimientos['_error'])) {
            return [$requerimientos['_error']];
        }

        $faltantes = [];

        foreach ($requerimientos['insumos'] as $insumoId => $cantidad) {
            $disponible = $this->obtenerStockInsumo((int) $insumoId);
            if ($disponible + 0.00001 < $cantidad) {
                $nombre = $this->nombreInsumo((int) $insumoId);
                $faltantes[] = "Insumo \"{$nombre}\": faltan {$this->formatearCantidad($cantidad - $disponible)} (requeridos {$this->formatearCantidad($cantidad)}, disponibles {$this->formatearCantidad($disponible)}).";
            }
        }

        foreach ($requerimientos['productos'] as $productoId => $cantidad) {
            $disponible = $this->obtenerStockProducto((int) $productoId);
            if ($disponible + 0.00001 < $cantidad) {
                $nombre = $this->nombreProducto((int) $productoId);
                $faltantes[] = "Producto \"{$nombre}\": faltan {$this->formatearCantidad($cantidad - $disponible)} (requeridos {$this->formatearCantidad($cantidad)}, disponibles {$this->formatearCantidad($disponible)}).";
            }
        }

        return $faltantes;
    }

    /**
     * @param array{insumos: array<int, float>, productos: array<int, float>, _error?: string} $requerimientos
     */
    private function validarRequerimientos(array $requerimientos): ?string
    {
        $faltantes = $this->listarFaltantesDesdeRequerimientos($requerimientos);

        return empty($faltantes) ? null : $this->mensajeNoSePuedeCompletar($faltantes);
    }

    /**
     * @param array{insumos: array<int, float>, productos: array<int, float>} $requerimientos
     */
    private function descontarRequerimientos(array $requerimientos): void
    {
        foreach ($requerimientos['insumos'] as $insumoId => $cantidad) {
            $this->descontarInsumo((int) $insumoId, (float) $cantidad);
        }

        foreach ($requerimientos['productos'] as $productoId => $cantidad) {
            $this->descontarProducto((int) $productoId, (float) $cantidad);
        }
    }

    private function descontarInsumo(int $insumoId, float $cantidad): void
    {
        $restante = $cantidad;

        $existencias = Existencia::query()
            ->where('id_insumo', $insumoId)
            ->where('cantidad', '>', 0)
            ->orderBy('id_almacen')
            ->lockForUpdate()
            ->get();

        foreach ($existencias as $existencia) {
            if ($restante <= 0) {
                break;
            }

            $deducir = min((float) $existencia->cantidad, $restante);
            $existencia->cantidad = (float) $existencia->cantidad - $deducir;
            $existencia->save();
            $restante -= $deducir;
        }
    }

    private function descontarProducto(int $productoId, float $cantidad): void
    {
        $restante = $cantidad;

        $existencias = Existencia::query()
            ->where('id_producto', $productoId)
            ->where('cantidad', '>', 0)
            ->orderBy('id_almacen')
            ->lockForUpdate()
            ->get();

        foreach ($existencias as $existencia) {
            if ($restante <= 0) {
                break;
            }

            $deducir = min((float) $existencia->cantidad, $restante);
            $existencia->cantidad = (float) $existencia->cantidad - $deducir;
            $existencia->save();
            $restante -= $deducir;
        }
    }

    private function obtenerStockInsumo(int $insumoId): float
    {
        return (float) Existencia::query()
            ->where('id_insumo', $insumoId)
            ->sum('cantidad');
    }

    private function obtenerStockProducto(int $productoId): float
    {
        return (float) Existencia::query()
            ->where('id_producto', $productoId)
            ->sum('cantidad');
    }

    private function esCortinero(Producto $producto): bool
    {
        $tipoNombre = strtolower($producto->tipoProducto->nombre ?? '');

        return (int) $producto->id_tipo_producto === 1 || $tipoNombre === 'cortinero';
    }

    private function acumularCantidad(array &$acumulado, int $id, float $cantidad): void
    {
        if ($id <= 0 || $cantidad <= 0) {
            return;
        }

        $acumulado[$id] = ($acumulado[$id] ?? 0) + $cantidad;
    }

    private function nombreInsumo(int $insumoId): string
    {
        $insumo = Insumo::with('proveedor')->find($insumoId);

        return $insumo ? ($insumo->nombre_completo ?: $insumo->nombre) : "ID {$insumoId}";
    }

    private function nombreProducto(int $productoId): string
    {
        $producto = Producto::find($productoId);

        return $producto ? $producto->nombre : "ID {$productoId}";
    }

    private function formatearCantidad(float $cantidad): string
    {
        $formateado = number_format($cantidad, 2, '.', '');

        return rtrim(rtrim($formateado, '0'), '.');
    }
}
