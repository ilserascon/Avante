<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PlantillaImportacionExport;
use App\Http\Controllers\Controller;
use App\Models\TipoInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class TiposInsumosController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoInsumo::query();

        if ($request->has('nombre') && $request->nombre != '') {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        // $query->where('borrado', 0);

        $tipoInsumos = $query->paginate(10);

        return view('admin.tipo_insumos.index', compact('tipoInsumos'));
    }

    public function create()
    {
        return view('admin.tipo_insumos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'  => 'required|string|max:255',
            'campo1'  => 'nullable|string|max:255',
            'campo2'  => 'nullable|string|max:255',
            'campo3'  => 'nullable|string|max:255',
            'campo4'  => 'nullable|string|max:255',
            'campo5'  => 'nullable|string|max:255',
            'campo6'  => 'nullable|string|max:255',
            'campo7'  => 'nullable|string|max:255',
            'campo8'  => 'nullable|string|max:255',
            'campo9'  => 'nullable|string|max:255',
            'campo10' => 'nullable|string|max:255',
            'campo11' => 'nullable|string|max:255',
            'campo12' => 'nullable|string|max:255',
            'campo13' => 'nullable|string|max:255',
            'campo14' => 'nullable|string|max:255',
            'campo15' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'campo1.max' => 'El campo 1 no debe exceder 255 caracteres.',
            'campo2.max' => 'El campo 2 no debe exceder 255 caracteres.',
            'campo3.max' => 'El campo 3 no debe exceder 255 caracteres.',
            'campo4.max' => 'El campo 4 no debe exceder 255 caracteres.',
            'campo5.max' => 'El campo 5 no debe exceder 255 caracteres.',
            'campo6.max' => 'El campo 6 no debe exceder 255 caracteres.',
            'campo7.max' => 'El campo 7 no debe exceder 255 caracteres.',
            'campo8.max' => 'El campo 8 no debe exceder 255 caracteres.',
            'campo9.max' => 'El campo 9 no debe exceder 255 caracteres.',
            'campo10.max' => 'El campo 10 no debe exceder 255 caracteres.',
            'campo11.max' => 'El campo 11 no debe exceder 255 caracteres.',
            'campo12.max' => 'El campo 12 no debe exceder 255 caracteres.',
            'campo13.max' => 'El campo 13 no debe exceder 255 caracteres.',
            'campo14.max' => 'El campo 14 no debe exceder 255 caracteres.',
            'campo15.max' => 'El campo 15 no debe exceder 255 caracteres.',
        ]);

        TipoInsumo::create($validated);

        return redirect()->route('admin.tipo-insumos.index')->with('success', 'Tipo de insumo creado exitosamente');
    }

    public function edit($id)
    {
        $tipoInsumo = TipoInsumo::findOrFail($id);
        return view('admin.tipo_insumos.edit', compact('tipoInsumo'));
    }

    public function update(Request $request, $id)
    {
        $tipoInsumo = TipoInsumo::findOrFail($id);

        $validated = $request->validate([
            'nombre'  => 'required|string|max:255',
            'campo1'  => 'nullable|string|max:255',
            'campo2'  => 'nullable|string|max:255',
            'campo3'  => 'nullable|string|max:255',
            'campo4'  => 'nullable|string|max:255',
            'campo5'  => 'nullable|string|max:255',
            'campo6'  => 'nullable|string|max:255',
            'campo7'  => 'nullable|string|max:255',
            'campo8'  => 'nullable|string|max:255',
            'campo9'  => 'nullable|string|max:255',
            'campo10' => 'nullable|string|max:255',
            'campo11' => 'nullable|string|max:255',
            'campo12' => 'nullable|string|max:255',
            'campo13' => 'nullable|string|max:255',
            'campo14' => 'nullable|string|max:255',
            'campo15' => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'campo1.max' => 'El campo 1 no debe exceder 255 caracteres.',
            'campo2.max' => 'El campo 2 no debe exceder 255 caracteres.',
            'campo3.max' => 'El campo 3 no debe exceder 255 caracteres.',
            'campo4.max' => 'El campo 4 no debe exceder 255 caracteres.',
            'campo5.max' => 'El campo 5 no debe exceder 255 caracteres.',
            'campo6.max' => 'El campo 6 no debe exceder 255 caracteres.',
            'campo7.max' => 'El campo 7 no debe exceder 255 caracteres.',
            'campo8.max' => 'El campo 8 no debe exceder 255 caracteres.',
            'campo9.max' => 'El campo 9 no debe exceder 255 caracteres.',
            'campo10.max' => 'El campo 10 no debe exceder 255 caracteres.',
            'campo11.max' => 'El campo 11 no debe exceder 255 caracteres.',
            'campo12.max' => 'El campo 12 no debe exceder 255 caracteres.',
            'campo13.max' => 'El campo 13 no debe exceder 255 caracteres.',
            'campo14.max' => 'El campo 14 no debe exceder 255 caracteres.',
            'campo15.max' => 'El campo 15 no debe exceder 255 caracteres.',
        ]);

        $tipoInsumo->update($validated);

        return redirect()->route('admin.tipo-insumos.index')->with('success', 'Tipo de insumo actualizado exitosamente');
    }

    /** Excel vacio con los encabezados que pide el importador de insumos de este tipo. */
    public function plantillaImportacion($id)
    {
        $tipoInsumo = TipoInsumo::findOrFail($id);

        $plantilla = PlantillaImportacionExport::paraTipo(
            ['clave', 'nombre', 'color', 'proveedor', 'costo', 'precio_publico', 'utilidad'],
            $tipoInsumo->camposPersonalizados(),
            (string) $tipoInsumo->nombre
        );

        $archivo = 'plantilla_insumos_' . Str::slug($tipoInsumo->nombre ?: 'tipo') . '.xlsx';

        return Excel::download($plantilla, $archivo);
    }
}