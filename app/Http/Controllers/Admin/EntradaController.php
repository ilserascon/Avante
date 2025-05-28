<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Existencia;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\TipoInsumo;
use Illuminate\Http\Request;

class EntradaController extends Controller 
{
    public function index()
    {
        $entradas = Entrada::with(['almacen', 'usuario'])->paginate(10); 
        return view('admin.entradas.index', compact('entradas'));
    }

    public function show($id)
    {
        $entrada = Entrada::with([
            'almacen',
            'usuario',
            'detalles.insumo.proveedor',
            'detalles.producto'
        ])->findOrFail($id);
        $tipos = TipoInsumo::all(); 
        $tipoSeleccionado = $entrada->detalles->first()->producto->tipo_insumo_id ?? null;
        return view('admin.entradas.show', compact('entrada', 'tipos', 'tipoSeleccionado'));
    }

    public function create()
    {
        $almacenes = Almacen::all();
        $productos = Producto::all();
        $insumos = DB::table('insumo')
            ->select(
                'insumo.id',
                DB::raw("TRIM(CONCAT_WS(' | ', 
                    COALESCE(insumo.nombre, ''), 
                    COALESCE(insumo.campo1, ''), 
                    COALESCE(insumo.campo2, ''), 
                    COALESCE((SELECT nombre FROM proveedores WHERE proveedores.id = insumo.id_proveedor), '')
                )) AS nombre_completo")
            )
            ->get();

        return view('admin.entradas.create', compact('almacenes', 'productos', 'insumos'));
    }

    public function edit($id)
    {
        $entrada = Entrada::with('detalles')->findOrFail($id);
        $almacenes = Almacen::all();
        $productos = Producto::all();
        $insumos = DB::table('insumo')
            ->select(
                'insumo.id',
                DB::raw("TRIM(CONCAT_WS(' | ', 
                    COALESCE(insumo.nombre, ''), 
                    COALESCE(insumo.campo1, ''), 
                    COALESCE(insumo.campo2, ''), 
                    COALESCE((SELECT nombre FROM proveedores WHERE proveedores.id = insumo.id_proveedor), '')
                )) AS nombre_completo")
            )
            ->get();

        return view('admin.entradas.edit', compact('entrada', 'almacenes', 'productos', 'insumos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_almacen' => 'required|exists:almacenes,id',
            'items' => 'required|array|min:1',
            'items.*.cantidad' => 'required|numeric|min:1',
            'items.*.id' => 'required',
            'items.*.tipo' => 'required|in:producto,insumo',
        ], [
            'id_almacen.required' => 'El campo almacén es obligatorio.',
            'id_almacen.exists' => 'El almacén seleccionado no es válido.',
            'items.required' => 'Debe agregar al menos un producto o insumo.',
            'items.array' => 'El formato de los items no es válido.',
            'items.min' => 'Debe agregar al menos un producto o insumo.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'items.*.id.required' => 'Debe seleccionar un producto o insumo.',
            'items.*.tipo.required' => 'Debe indicar el tipo (producto o insumo).',
            'items.*.tipo.in' => 'El tipo seleccionado no es válido.',
        ]);

        $entrada = Entrada::findOrFail($id);
        $entrada->update([
            'id_almacen' => $request->id_almacen,
        ]);

        foreach ($entrada->detalles as $detalle) {
            $campo = $detalle->id_producto ? 'id_producto' : 'id_insumo';
            $id = $detalle->$campo;

            $existencia = Existencia::where('id_almacen', $entrada->id_almacen)
                ->where($campo, $id)
                ->first();

            if ($existencia) {
                $existencia->cantidad -= $detalle->cantidad;
                $existencia->save();
            }

            $detalle->delete();
        }

        foreach ($request->items as $item) {
            $id_producto = $item['tipo'] === 'producto' ? $item['id'] : null;
            $id_insumo = $item['tipo'] === 'insumo' ? $item['id'] : null;

            if ($id_producto && !Producto::find($id_producto)) {
                return back()->withErrors(['Producto inválido: no existe en la base de datos']);
            }
            if ($id_insumo && !Insumo::find($id_insumo)) {
                return back()->withErrors(['Insumo inválido: no existe en la base de datos']);
            }

            $detalle = new DetalleEntrada([
                'id_producto' => $id_producto,
                'id_insumo' => $id_insumo,
                'cantidad' => $item['cantidad'],
            ]);
            $entrada->detalles()->save($detalle);

            $campo = $item['tipo'] === 'producto' ? 'id_producto' : 'id_insumo';

            $existencia = Existencia::where('id_almacen', $entrada->id_almacen)
                ->where($campo, $item['id'])
                ->first();

            if ($existencia) {
                $existencia->cantidad += $item['cantidad'];
                $existencia->save();
            } else {
                Existencia::create([
                    'id_almacen' => $entrada->id_almacen,
                    'id_producto' => $id_producto,
                    'id_insumo' => $id_insumo,
                    'cantidad' => $item['cantidad'],
                ]);
            }
        }

        return redirect()->route('admin.entradas.index')->with('success', 'Entrada actualizada correctamente.');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'id_almacen' => 'required|exists:almacenes,id',
            'items' => 'required|array|min:1',
            'items.*.cantidad' => 'required|numeric|min:1',
            'items.*.id_producto' => 'nullable|exists:productos,id',
            'items.*.id_insumo' => 'nullable|exists:insumo,id',
        ], [
            'id_almacen.required' => 'El campo almacén es obligatorio.',
            'id_almacen.exists' => 'El almacén seleccionado no es válido.',
            'items.required' => 'Debe agregar al menos un producto o insumo.',
            'items.array' => 'El formato de los items no es válido.',
            'items.min' => 'Debe agregar al menos un producto o insumo.',
            'items.*.cantidad.required' => 'La cantidad es obligatoria.',
            'items.*.cantidad.numeric' => 'La cantidad debe ser un número.',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'items.*.id_producto.exists' => 'El producto seleccionado no es válido.',
            'items.*.id_insumo.exists' => 'El insumo seleccionado no es válido.',
        ]);

        $entrada = Entrada::create([
            'id_almacen' => $request->id_almacen,
            'id_usuario' => auth()->id(),
        ]);

        foreach ($request->items as $item) {
            if (!empty($item['id_producto'])) {
                $id_producto = $item['id_producto'];
                $id_insumo = null;
                $tipo = 'producto';
                $id = $id_producto;
            } elseif (!empty($item['id_insumo'])) {
                $id_producto = null;
                $id_insumo = $item['id_insumo'];
                $tipo = 'insumo';
                $id = $id_insumo;
            } else {
                continue; 
            }

            $detalle = new DetalleEntrada([
                'id_producto' => $id_producto,
                'id_insumo' => $id_insumo,
                'cantidad' => $item['cantidad'],
            ]);
            $entrada->detalles()->save($detalle);

            $campo = $tipo === 'producto' ? 'id_producto' : 'id_insumo';

            $existencia = Existencia::where('id_almacen', $entrada->id_almacen)
                ->where($campo, $id)
                ->first();

            if ($existencia) {
                $existencia->cantidad += $item['cantidad'];
                $existencia->save();
            } else {
                Existencia::create([
                    'id_almacen' => $entrada->id_almacen,
                    'id_producto' => $id_producto,
                    'id_insumo' => $id_insumo,
                    'cantidad' => $item['cantidad'],
                ]);
            }
        }

        return redirect()->route('admin.entradas.index')->with('success', 'Entrada registrada correctamente.');
    }

}