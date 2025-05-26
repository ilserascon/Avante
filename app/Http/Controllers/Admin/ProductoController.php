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

        $productos = $query->paginate(10);

        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
    $insumos = DB::table('insumo')
        ->leftJoin('proveedores', 'proveedores.id', '=', 'insumo.id_proveedor')
        ->select(
            'insumo.id',
            DB::raw("TRIM(CONCAT_WS(' | ', insumo.nombre, insumo.campo1, insumo.campo2, proveedores.nombre)) AS nombre_completo")
        )
        ->get();
        return view('admin.productos.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'insumos' => 'required|array',
            'insumos.*.id' => 'required|exists:insumo,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'insumos.required' => 'Debe agregar al menos un insumo.',
            'insumos.array' => 'El formato de insumos no es válido.',
            'insumos.*.id.required' => 'Debe seleccionar un insumo.',
            'insumos.*.id.exists' => 'El insumo seleccionado no es válido.',
            'insumos.*.cantidad.required' => 'La cantidad del insumo es obligatoria.',
            'insumos.*.cantidad.numeric' => 'La cantidad del insumo debe ser numérica.',
            'insumos.*.cantidad.min' => 'La cantidad del insumo no puede ser negativa.',
        ]);

        $producto = Producto::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion']
        ]);

        foreach ($validated['insumos'] as $insumo) {
            ProductoInsumo::create([
                'id_producto' => $producto->id,
                'id_insumo' => $insumo['id'],
                'cantidad' => $insumo['cantidad'],
            ]);
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
    }


    public function edit($id)
    {
        $producto = Producto::with('insumos')->findOrFail($id);

        $insumos = DB::table('insumo')
            ->leftJoin('proveedores', 'proveedores.id', '=', 'insumo.id_proveedor')
            ->select(
                'insumo.id',
                DB::raw("TRIM(CONCAT_WS(' | ', insumo.nombre, insumo.campo1, insumo.campo2, proveedores.nombre)) AS nombre_completo")
            )
            ->get();

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
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'insumos.array' => 'El formato de insumos no es válido.',
            'insumos.*.id.required' => 'Debe seleccionar un insumo.',
            'insumos.*.id.exists' => 'El insumo seleccionado no es válido.',
            'insumos.*.cantidad.required' => 'La cantidad del insumo es obligatoria.',
            'insumos.*.cantidad.numeric' => 'La cantidad del insumo debe ser numérica.',
            'insumos.*.cantidad.min' => 'La cantidad del insumo no puede ser negativa.',
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

        ProductoInsumo::where('id_producto', $producto->id)
            ->whereNotIn('id_insumo', $insumoIds)
            ->delete();

        foreach ($insumos ?? [] as $insumo) {
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
                'insumo.*',
                DB::raw("TRIM(CONCAT_WS(' | ', insumo.nombre, insumo.campo1, insumo.campo2, proveedores.nombre)) AS nombre_completo")
            )
            ->leftJoin('proveedores', 'proveedores.id', '=', 'insumo.id_proveedor');
        }])->findOrFail($id);

        return view('admin.productos.insumos', compact('producto'));
    }

}
