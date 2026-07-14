<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoProducto;
use Illuminate\Http\Request;

class TipoProductosController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoProducto::query();

        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        $tipoProductos = $query->paginate(10);

        return view('admin.tipo_productos.index', compact('tipoProductos'));
    }

    public function create()
    {
        return view('admin.tipo_productos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder 500 caracteres.',
        ]);

        TipoProducto::create($validated);

        return redirect()->route('admin.tipo-productos.index')->with('success', 'Tipo de producto creado exitosamente');
    }

    public function edit($id)
    {
        $tipoProducto = TipoProducto::findOrFail($id);

        return view('admin.tipo_productos.edit', compact('tipoProducto'));
    }

    public function update(Request $request, $id)
    {
        $tipoProducto = TipoProducto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder 500 caracteres.',
        ]);

        $tipoProducto->update($validated);

        return redirect()->route('admin.tipo-productos.index')->with('success', 'Tipo de producto actualizado exitosamente');
    }
}
