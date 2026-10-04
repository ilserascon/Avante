<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Existencia;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\TipoInsumo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $almacenes = Almacen::query()->paginate(10);

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
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'ubicacion.max' => 'El campo ubicación no debe exceder 255 caracteres.',
        ]);

        Almacen::create($request->all());

        return redirect()->route('admin.almacenes.index')->with('success', 'Almacén creado correctamente.');
    }

    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'ubicacion.max' => 'El campo ubicación no debe exceder 255 caracteres.',
        ]);

        $almacen->update($request->all());

        return redirect()->route('admin.almacenes.index')->with('success', 'Almacén actualizado.');
    }

    public function edit($id)
    {
        $almacen = Almacen::findOrFail($id);
        return view('admin.almacenes.edit', compact('almacen'));
    }

    public function showExistencia($id, Request $request)
    {
        $almacen = Almacen::findOrFail($id);
        $tipo = $request->get('tipo');

        if (!in_array($tipo, ['producto', 'insumo'], true)) {
            $existencias = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return view('admin.almacenes.existencia', compact('almacen', 'existencias'));
        }

        $existenciasQuery = $almacen->existencias()->with(['producto.tipoProducto', 'insumo.tipoInsumo']);

        if ($tipo === 'producto') {
            $existenciasQuery->whereNotNull('existencia.id_producto');

            if ($request->filled('producto')) {
                $termino = $request->producto;
                $existenciasQuery->whereHas('producto', function ($q) use ($termino) {
                    $q->where(function ($inner) use ($termino) {
                        $inner->where('nombre', 'like', '%' . $termino . '%')
                            ->orWhere('clave', 'like', '%' . $termino . '%')
                            ->orWhere('color', 'like', '%' . $termino . '%');
                    });
                });
            }

            $existenciasQuery
                ->leftJoin('productos', 'existencia.id_producto', '=', 'productos.id')
                ->orderBy('productos.nombre')
                ->orderBy('productos.color')
                ->select('existencia.*');
        } else {
            $existenciasQuery->whereNotNull('existencia.id_insumo');

            if ($request->filled('insumo')) {
                $termino = $request->insumo;
                $existenciasQuery->whereHas('insumo', function ($q) use ($termino) {
                    $q->where(function ($inner) use ($termino) {
                        $inner->where('nombre', 'like', '%' . $termino . '%')
                            ->orWhere('clave', 'like', '%' . $termino . '%')
                            ->orWhere('color', 'like', '%' . $termino . '%');
                    });
                });
            }

            $existenciasQuery
                ->leftJoin('insumo', 'existencia.id_insumo', '=', 'insumo.id')
                ->orderBy('insumo.nombre')
                ->orderBy('insumo.color')
                ->orderBy('insumo.campo1')
                ->select('existencia.*');
        }

        $existencias = $existenciasQuery->paginate(10)->appends($request->query());

        $existencias->getCollection()->transform(function (Existencia $existencia) use ($tipo) {
            if ($tipo === 'producto') {
                return [
                    'producto' => $existencia->producto?->etiquetaEntrada() ?? '-',
                    'cantidad_producto' => $existencia->producto ? $existencia->cantidad : '-',
                ];
            }

            return [
                'insumo' => $existencia->insumo?->etiquetaEntrada() ?? '-',
                'cantidad_insumo' => $existencia->insumo ? $existencia->cantidad : '-',
            ];
        });

        return view('admin.almacenes.existencia', compact('almacen', 'existencias'));
    }
}

