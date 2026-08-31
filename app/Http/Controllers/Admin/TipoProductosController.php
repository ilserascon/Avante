<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PlantillaImportacionExport;
use App\Http\Controllers\Controller;
use App\Models\TipoProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

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
        $validated = $request->validate(array_merge([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ], $this->reglasCampos()), $this->mensajesValidacion());

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

        $validated = $request->validate(array_merge([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
        ], $this->reglasCampos()), $this->mensajesValidacion());

        $tipoProducto->update($validated);

        return redirect()->route('admin.tipo-productos.index')->with('success', 'Tipo de producto actualizado exitosamente');
    }

    /** Excel vacio con los encabezados que pide el importador de productos de este tipo. */
    public function plantillaImportacion($id)
    {
        $tipoProducto = TipoProducto::findOrFail($id);

        $plantilla = PlantillaImportacionExport::paraTipo(
            ['clave', 'nombre', 'precio', 'precio_publico', 'color', 'descripcion', 'proveedor'],
            $tipoProducto->camposPersonalizados(),
            (string) $tipoProducto->nombre
        );

        $archivo = 'plantilla_productos_' . Str::slug($tipoProducto->nombre ?: 'tipo') . '.xlsx';

        return Excel::download($plantilla, $archivo);
    }

    private function reglasCampos(): array
    {
        $rules = [];

        for ($i = 1; $i <= TipoProducto::CAMPOS_DINAMICOS; $i++) {
            $rules['campo' . $i] = 'nullable|string|max:255';
        }

        return $rules;
    }

    private function mensajesValidacion(): array
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder 500 caracteres.',
        ];
    }
}
