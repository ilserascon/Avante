<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InventarioExport;
use App\Http\Controllers\Controller;
use App\Models\Existencia;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $nombre = trim((string) $request->input('nombre', ''));
        $tipo = $request->input('tipo', '');
        $items = $this->recopilarInventario($nombre, (string) $tipo);

        $page = max(1, (int) $request->input('page', 1));
        $perPage = 15;
        $total = count($items);
        $pagina = array_slice($items, ($page - 1) * $perPage, $perPage);

        $inventario = new LengthAwarePaginator(
            $pagina,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.inventario.index', compact('inventario', 'nombre', 'tipo'));
    }

    public function export(Request $request)
    {
        $nombre = trim((string) $request->input('nombre', ''));
        $tipo = $request->input('tipo', '');
        $items = $this->recopilarInventario($nombre, (string) $tipo);

        $archivo = 'inventario_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new InventarioExport($items), $archivo);
    }

    /**
     * @return list<array{tipo: string, nombre: string, cantidad_total: float, almacenes: list<array{nombre: string, cantidad: float}>}>
     */
    private function recopilarInventario(string $nombre, string $tipo): array
    {
        $query = Existencia::query()
            ->with(['producto.tipoProducto', 'insumo.tipoInsumo', 'almacen'])
            ->where('cantidad', '>', 0);

        if ($tipo === 'producto') {
            $query->whereNotNull('id_producto');
        } elseif ($tipo === 'insumo') {
            $query->whereNotNull('id_insumo');
        }

        if ($nombre !== '') {
            $query->where(function ($q) use ($nombre) {
                $q->whereHas('producto', function ($productoQuery) use ($nombre) {
                    $productoQuery->where('nombre', 'like', '%' . $nombre . '%')
                        ->orWhere('clave', 'like', '%' . $nombre . '%')
                        ->orWhere('color', 'like', '%' . $nombre . '%');
                })->orWhereHas('insumo', function ($insumoQuery) use ($nombre) {
                    $insumoQuery->where('nombre', 'like', '%' . $nombre . '%')
                        ->orWhere('clave', 'like', '%' . $nombre . '%')
                        ->orWhere('color', 'like', '%' . $nombre . '%')
                        ->orWhere('campo1', 'like', '%' . $nombre . '%')
                        ->orWhere('campo2', 'like', '%' . $nombre . '%');
                });
            });
        }

        $agrupado = [];

        foreach ($query->get() as $existencia) {
            if ($existencia->id_producto && $existencia->producto) {
                $key = 'producto_' . $existencia->id_producto;
                if (!isset($agrupado[$key])) {
                    $agrupado[$key] = [
                        'tipo' => 'Producto',
                        'nombre' => $existencia->producto->etiquetaEntrada(),
                        'cantidad_total' => 0.0,
                        'almacenes' => [],
                    ];
                }
            } elseif ($existencia->id_insumo && $existencia->insumo) {
                $key = 'insumo_' . $existencia->id_insumo;
                if (!isset($agrupado[$key])) {
                    $agrupado[$key] = [
                        'tipo' => 'Insumo',
                        'nombre' => $existencia->insumo->etiquetaEntrada(),
                        'cantidad_total' => 0.0,
                        'almacenes' => [],
                    ];
                }
            } else {
                continue;
            }

            $cantidad = (float) $existencia->cantidad;
            $agrupado[$key]['cantidad_total'] += $cantidad;
            $agrupado[$key]['almacenes'][] = [
                'nombre' => $existencia->almacen?->nombre ?? 'Sin almacén',
                'cantidad' => $cantidad,
            ];
        }

        usort($agrupado, fn (array $a, array $b) => strcasecmp($a['nombre'], $b['nombre']));

        return array_values($agrupado);
    }
}
