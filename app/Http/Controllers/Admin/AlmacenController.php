<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Existencia;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\TipoInsumo;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $query = Almacen::query();

        if ($request->has('nombre') && $request->nombre != '') {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }
    
        $almacenes = $query->paginate(10); 
    
        return view('admin.almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        return view('admin.almacenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        Almacen::create($request->all());

        return redirect()->route('admin.almacenes.index')->with('success', 'Almacén creado correctamente.');
    }

    public function edit(Almacen $almacen)
    {
        return view('admin.almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        $almacen->update($request->all());

        return redirect()->route('admin.almacenes.index')->with('success', 'Almacén actualizado.');
    }

    public function showExistencia($id, Request $request)
    {
        $almacen = Almacen::findOrFail($id);

        $existenciasQuery = $almacen->existencias()->with(['producto', 'insumo']);

        // Filtros
        if ($request->filled('producto')) {
            $existenciasQuery->whereHas('producto', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->producto . '%');
            });
        }
        if ($request->filled('insumo')) {
            $existenciasQuery->whereHas('insumo', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->insumo . '%');
            });
        }
        if ($request->filled('existencia')) {
            $existenciasQuery->where('cantidad', $request->existencia);
        }
        if ($request->filled('nombre')) {
            $existenciasQuery->where(function ($q) use ($request) {
                $q->whereHas('producto', function ($q2) use ($request) {
                    $q2->where('nombre', 'like', '%' . $request->nombre . '%');
                })->orWhereHas('insumo', function ($q3) use ($request) {
                    $q3->where('nombre', 'like', '%' . $request->nombre . '%');
                });
            });
        }

        $existencias = $existenciasQuery->paginate(10);

        // Prepara los datos para la vista según el filtro 'tipo'
        $tipo = $request->get('tipo');
        $filas = [];
        foreach ($existencias as $existencia) {
            if ($tipo == 'producto') {
                if ($existencia->producto) {
                    $filas[] = [
                        'producto' => $existencia->producto->nombre,
                        'cantidad_producto' => $existencia->cantidad,
                    ];
                }
            } elseif ($tipo == 'insumo') {
                if ($existencia->insumo) {
                    $filas[] = [
                        'insumo' => $existencia->insumo->nombre_completo ?? $existencia->insumo->nombre ?? '-',
                        'cantidad_insumo' => $existencia->cantidad,
                    ];
                }
            } else {
                // Ambos
                $filas[] = [
                    'producto' => $existencia->producto->nombre ?? '-',
                    'cantidad_producto' => $existencia->producto ? $existencia->cantidad : '-',
                    'insumo' => $existencia->insumo->nombre_completo ?? $existencia->insumo->nombre ?? '-',
                    'cantidad_insumo' => $existencia->insumo ? $existencia->cantidad : '-',
                ];
            }
        }

        // Pagina manualmente las filas
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $existenciasPaginadas = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($filas, $offset, $perPage),
            count($filas),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.almacenes.existencia', [
            'almacen' => $almacen,
            'existencias' => $existenciasPaginadas
        ]);
    }
}

