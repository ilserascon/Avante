<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoInsumo;
use Illuminate\Http\Request;

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
        ]);

        $tipoInsumo->update($validated);

        return redirect()->route('admin.tipo-insumos.index')->with('success', 'Tipo de insumo actualizado exitosamente');
    }
}
