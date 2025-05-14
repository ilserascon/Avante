<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotizacion;

use App\Models\Insumo;

class CotizacionController extends Controller
{
    public function index()
    {
        $cotizaciones = Cotizacion::latest()->paginate(10);
        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $insumos = Insumo::where('id_tipo_insumo', '!=', 1)->get();
        return view('admin.cotizaciones.create', compact('insumos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'required|exists:productos,id',
            'insumo_id' => 'required|exists:insumos,id',
            'tipo_insumo_id' => 'required|exists:tipo_insumos,id',
            'detalles' => 'required|array',
            'total' => 'required|numeric',
        ]);

        $cotizacion = new Cotizacion();
        $cotizacion->cliente_id = $validated['cliente_id'];
        $cotizacion->producto_id = $validated['producto_id'];
        $cotizacion->insumo_id = $validated['insumo_id'];
        $cotizacion->tipo_insumo_id = $validated['tipo_insumo_id'];
        $cotizacion->detalles = json_encode($validated['detalles']);
        $cotizacion->total = $validated['total'];
        $cotizacion->estatus = 'pendiente';
        $cotizacion->save();

        return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización creada exitosamente.');
    }


    public function show($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        return view('admin.cotizaciones.show', compact('cotizacion'));
    }

    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
