<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotizacion;

use App\Models\Insumo;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::query();

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        $cotizaciones = $query->latest()->paginate(10);

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $insumos = Insumo::where('id_tipo_insumo', '!=', 1)->get();
        return view('admin.cotizaciones.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        // Validación ajustada según la migración y el formulario
        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'fecha'              => 'required|date',
            // Los siguientes campos pueden ser opcionales según el formulario
            'detalle'            => 'array',
            'totales'            => 'array',
        ]);

        $detalle = $request->input('detalle', []);
        $totales = $request->input('totales', []);

        $cotizacion = new Cotizacion();
        $cotizacion->cliente_id   = $validated['cliente_id'];
        $cotizacion->fecha        = $validated['fecha'];

        // Flags de tipo de cotización
        $tipos = $request->input('tipo', []);
        $cotizacion->lleva_cortina = in_array('cortina', $tipos);
        $cotizacion->lleva_tergal  = in_array('tergal', $tipos);
        $cotizacion->lleva_forro   = $request->has('lleva_forro');

        // Totales y cálculos
        $cotizacion->total_lienzos     = $totales['total_lienzos'] ?? null;
        $cotizacion->total_m2_forro    = $totales['total_m2_forro'] ?? null;
        $cotizacion->total_m2_tela     = $totales['total_m2_tela'] ?? null;
        $cotizacion->total_m2_tergal   = $totales['total_m2_tergal'] ?? null;
        $cotizacion->costo_cortina     = $totales['costo_cortina'] ?? null;
        $cotizacion->utilidad          = $totales['utilidad'] ?? null;
        $cotizacion->costo_decorador   = $totales['costo_decorador'] ?? null;
        $cotizacion->precio_publico    = $totales['precio_publico'] ?? null;

        // Puedes guardar el detalle completo como JSON si lo necesitas para referencia
        // $cotizacion->detalles = json_encode($detalle);

        // $cotizacion->estatus = 'pendiente';

        $cotizacion->save();

        return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización creada exitosamente.');
    }

    public function show($id)
    {
        $cotizacion = Cotizacion::with('cliente')->findOrFail($id);
        return view('admin.cotizaciones.show', compact('cotizacion'));
    }

    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
