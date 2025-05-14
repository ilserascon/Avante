<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\ProductoInsumo;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        $productos = $query->get();

        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        $insumos = Insumo::select(
            'insumo.id',
            DB::raw("TRIM(CONCAT_WS(' | ', insumo.nombre, insumo.campo1, insumo.campo2, proveedores.nombre)) AS nombre_completo")
        )
        ->leftJoin('proveedores', 'proveedores.id', '=', 'insumo.id_proveedor')
        ->distinct() // Evita duplicados
        ->get();

        return view('admin.productos.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'insumos' => 'required|array',
            'insumos.*' => 'exists:insumos,id'
        ]);

        // Crear el producto
        $producto = Producto::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion']
        ]);

        // Asociar los insumos al producto
        foreach ($validated['insumos'] as $insumoId) {
            ProductoInsumo::create([
                'id_producto' => $producto->id,
                'id_insumo' => $insumoId,
                'cantidad' => 1, // Puedes ajustar la cantidad según tus necesidades
            ]);
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
    }


    public function edit($id)
    {
        $producto = Producto::with('insumos')->findOrFail($id);

        $insumos = Insumo::select(
            'insumo.id',
            DB::raw("TRIM(CONCAT_WS(' | ', 
                COALESCE(insumo.nombre, ''), 
                COALESCE(insumo.campo1, ''), 
                COALESCE(insumo.campo2, ''), 
                COALESCE((SELECT nombre FROM proveedores WHERE proveedores.id = insumo.id_proveedor), '')
            )) AS nombre_completo")
        )->get();

        return view('admin.productos.edit', compact('producto', 'insumos'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'insumos' => 'sometimes|array',
            'insumos.*.id' => 'required|exists:insumo,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $producto) {
            $producto->update($request->only('nombre', 'descripcion'));
            $this->syncInsumos($producto, $request->insumos);
        });

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    private function syncInsumos(Producto $producto, $insumos)
    {
        $insumoIds = collect($insumos)->pluck('id')->toArray();

        // Eliminar insumos que ya no están asociados al producto
        ProductoInsumo::where('id_producto', $producto->id)
            ->whereNotIn('id_insumo', $insumoIds)
            ->delete();

        foreach ($insumos ?? [] as $insumo) {
            // Actualizar o insertar el insumo en la tabla pivote
            ProductoInsumo::updateOrInsert(
                [
                    'id_producto' => $producto->id,
                    'id_insumo' => $insumo['id'],
                ],
                [
                    'cantidad' => $insumo['cantidad'],
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function verInsumos($id)
    {
        $producto = Producto::with(['insumos' => function ($query) {
            $query->select(
                'insumo.id',
                DB::raw("CONCAT(
                    COALESCE(insumo.nombre, ''), ' | ',
                    COALESCE(insumo.campo1, ''), ' | ',
                    COALESCE(insumo.campo2, ''), ' | ',
                    COALESCE((SELECT nombre FROM proveedores WHERE proveedores.id = insumo.id_proveedor), '')
                ) AS nombre_completo")
            );
        }])->findOrFail($id);

        return view('admin.productos.insumos', compact('producto'));
    }

}
