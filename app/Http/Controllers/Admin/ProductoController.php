<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\ProductoInsumo;
use App\Models\TipoProducto;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureTipoProductoCatalogExists();

        $query = Producto::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        if ($request->filled('id_tipo_producto')) {
            $query->where('id_tipo_producto', $request->id_tipo_producto);
        }

        $productos = $query->with('tipoProducto')->paginate(10);
        $tiposProducto = TipoProducto::orderBy('nombre')->get();

        return view('admin.productos.index', compact('productos', 'tiposProducto'));
    }

    public function create()
    {
        $this->ensureTipoProductoCatalogExists();

        $insumos = DB::table('insumo')
            ->leftJoin('proveedores', 'proveedores.id', '=', 'insumo.id_proveedor')
            ->select(
                'insumo.id',
                DB::raw("TRIM(CONCAT_WS(' | ', insumo.nombre, insumo.campo1, insumo.campo2, proveedores.nombre)) AS nombre_completo")
            )
            ->get();

        $tiposProducto = TipoProducto::orderBy('nombre')->get();

        return view('admin.productos.create', compact('insumos', 'tiposProducto'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'id_tipo_producto' => 'nullable|exists:tipo_producto,id',
            'insumos' => 'nullable|array',
            'insumos.*.id' => 'required|exists:insumo,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ]);

        $producto = Producto::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'precio' => $validated['precio'] ?? null,
            'id_tipo_producto' => $validated['id_tipo_producto'] ?? null,
        ]);

        if (!empty($validated['insumos']) && ($validated['id_tipo_producto'] == 1 || strtolower(optional(TipoProducto::find($validated['id_tipo_producto']))->nombre ?? '') === 'cortinero')) {
            foreach ($validated['insumos'] as $insumo) {
                ProductoInsumo::create([
                    'id_producto' => $producto->id,
                    'id_insumo' => $insumo['id'],
                    'cantidad' => $insumo['cantidad'],
                ]);
            }
        }

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit($id)
    {
        $this->ensureTipoProductoCatalogExists();

        $producto = Producto::with('insumos.proveedor')->findOrFail($id);

        $insumos = DB::table('insumo')
            ->leftJoin('proveedores', 'proveedores.id', '=', 'insumo.id_proveedor')
            ->select(
                'insumo.id',
                DB::raw("TRIM(CONCAT_WS(' | ', insumo.nombre, insumo.campo1, insumo.campo2, proveedores.nombre)) AS nombre_completo")
            )
            ->get();

        $tiposProducto = TipoProducto::orderBy('nombre')->get();

        return view('admin.productos.edit', compact('producto', 'insumos', 'tiposProducto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'id_tipo_producto' => 'nullable|exists:tipo_producto,id',
            'insumos' => 'sometimes|array',
            'insumos.*.id' => 'required|exists:insumo,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $producto) {
            $producto->update($request->only('nombre', 'descripcion', 'precio', 'id_tipo_producto'));
            $this->syncInsumos($producto, $request->input('insumos', []));
        });

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    private function ensureTipoProductoCatalogExists(): void
    {
        if (TipoProducto::count() === 0) {
            TipoProducto::create([
                'nombre' => 'cortinero',
                'descripcion' => 'Tipo de producto para cortineros',
            ]);
        }
    }

    private function syncInsumos(Producto $producto, $insumos)
    {
        $insumoIds = collect($insumos)->pluck('id')->toArray();

        // Eliminar insumos que ya no están
        ProductoInsumo::where('id_producto', $producto->id)
            ->whereNotIn('id_insumo', $insumoIds)
            ->delete();

        // Insertar o actualizar insumos
        foreach ($insumos as $insumo) {
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
        // Carga el producto con insumos y el proveedor de cada insumo
        $producto = Producto::with('insumos.proveedor')->findOrFail($id);

        return view('admin.productos.insumos', compact('producto'));
    }
}
