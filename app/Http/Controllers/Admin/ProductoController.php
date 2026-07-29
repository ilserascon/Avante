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
        $tipoSeleccionado = $request->get('id_tipo_producto');
        $camposDinamicos = [];

        if ($request->filled('nombre')) {
            $termino = $request->nombre;
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'LIKE', '%' . $termino . '%')
                    ->orWhere('clave', 'LIKE', '%' . $termino . '%');
            });
        }

        if ($request->filled('id_tipo_producto')) {
            $tipo = TipoProducto::find($request->id_tipo_producto);
            if ($tipo) {
                $camposDinamicos = $tipo->camposPersonalizados();
            }
            $query->where('id_tipo_producto', $request->id_tipo_producto);
        }

        $productos = $query->with('tipoProducto')->paginate(10)->appends($request->query());
        $tiposProducto = TipoProducto::orderBy('nombre')->get();

        return view('admin.productos.index', compact('productos', 'tiposProducto', 'tipoSeleccionado', 'camposDinamicos'));
    }

    public function create()
    {
        $this->ensureTipoProductoCatalogExists();

        $insumos = DB::table('insumo')
            ->select('insumo.id', 'insumo.clave', 'insumo.nombre')
            ->orderBy('insumo.nombre')
            ->get()
            ->map(function ($insumo) {
                $modelo = new Insumo((array) $insumo);
                $insumo->etiqueta = $modelo->etiquetaClaveNombre();

                return $insumo;
            });

        $tiposProducto = $this->tiposProductoConCampos();

        return view('admin.productos.create', compact('insumos', 'tiposProducto'));
    }

    public function store(Request $request)
    {
        $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false;

        $validated = $request->validate(array_merge([
            'nombre' => 'required|string|max:255',
            'clave' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'precio_publico' => 'nullable|numeric|min:0',
            'id_tipo_producto' => 'nullable|exists:tipo_producto,id',
            'insumos' => 'nullable|array',
            'insumos.*.id' => 'required|exists:insumo,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ], $this->reglasCamposProducto()));

        $producto = Producto::create([
            'nombre' => $validated['nombre'],
            'clave' => $validated['clave'] ?? null,
            'color' => $validated['color'] ?? null,
            'descripcion' => $validated['descripcion'] ?? null,
            'precio' => $veCostos ? ($validated['precio'] ?? null) : null,
            'precio_publico' => $validated['precio_publico'] ?? null,
            'id_tipo_producto' => $validated['id_tipo_producto'] ?? null,
            ...$this->datosCamposProducto($validated),
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

    public function show(Producto $producto)
    {
        $this->ensureTipoProductoCatalogExists();

        $producto->load(['tipoProducto', 'insumos.proveedor']);
        $camposDinamicos = $producto->tipoProducto?->camposPersonalizados() ?? [];

        return view('admin.productos.show', compact('producto', 'camposDinamicos'));
    }

    public function edit($id)
    {
        $this->ensureTipoProductoCatalogExists();

        $producto = Producto::with(['insumos.proveedor', 'tipoProducto'])->findOrFail($id);

        $insumos = DB::table('insumo')
            ->select('insumo.id', 'insumo.clave', 'insumo.nombre')
            ->orderBy('insumo.nombre')
            ->get()
            ->map(function ($insumo) {
                $modelo = new Insumo((array) $insumo);
                $insumo->etiqueta = $modelo->etiquetaClaveNombre();

                return $insumo;
            });

        $tiposProducto = $this->tiposProductoConCampos();

        return view('admin.productos.edit', compact('producto', 'insumos', 'tiposProducto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false;

        $validated = $request->validate(array_merge([
            'nombre' => 'required|string|max:255',
            'clave' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'precio_publico' => 'nullable|numeric|min:0',
            'id_tipo_producto' => 'nullable|exists:tipo_producto,id',
            'insumos' => 'sometimes|array',
            'insumos.*.id' => 'required|exists:insumo,id',
            'insumos.*.cantidad' => 'required|numeric|min:0',
        ], $this->reglasCamposProducto()));

        DB::transaction(function () use ($request, $producto, $veCostos, $validated) {
            $datos = array_merge(
                $request->only('nombre', 'clave', 'color', 'descripcion', 'precio_publico', 'id_tipo_producto'),
                $this->datosCamposProducto($validated)
            );
            if ($veCostos) {
                $datos['precio'] = $request->input('precio');
            }
            $producto->update($datos);
            $this->syncInsumos($producto, $request->input('insumos', []));
        });

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function camposDinamicosPorTipo(Request $request)
    {
        $tipo = TipoProducto::find($request->id_tipo_producto);

        return response()->json($tipo?->camposPersonalizados() ?? []);
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

    private function tiposProductoConCampos()
    {
        return TipoProducto::orderBy('nombre')->get()->map(function ($tipo) {
            $tipo->campos_data = $tipo->camposPersonalizados();

            return $tipo;
        });
    }

    private function reglasCamposProducto(): array
    {
        $rules = [];

        for ($i = 1; $i <= TipoProducto::CAMPOS_DINAMICOS; $i++) {
            $rules['campo' . $i] = 'nullable|string|max:255';
        }

        return $rules;
    }

    private function datosCamposProducto(array $validated): array
    {
        $datos = [];

        for ($i = 1; $i <= TipoProducto::CAMPOS_DINAMICOS; $i++) {
            $campo = 'campo' . $i;
            $datos[$campo] = $validated[$campo] ?? null;
        }

        return $datos;
    }

    private function syncInsumos(Producto $producto, $insumos)
    {
        $insumoIds = collect($insumos)->pluck('id')->toArray();

        ProductoInsumo::where('id_producto', $producto->id)
            ->whereNotIn('id_insumo', $insumoIds)
            ->delete();

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
        $producto = Producto::with('insumos.proveedor')->findOrFail($id);

        return view('admin.productos.insumos', compact('producto'));
    }
}
