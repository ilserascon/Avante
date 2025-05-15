<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Existencia;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\TipoInsumo;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
{
    public function index(Request $request) // <-- Aquí se agrega el parámetro
    {
        $query = Almacen::query();

        if ($request->has('nombre') && $request->nombre != '') {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }
    
        $almacenes = $query->paginate(10); // Resultados filtrados
    
        return view('admin.almacenes.index', compact('almacenes'));
    }

    public function create()
    {
        return view('admin.almacenes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        Almacen::create($request->all());

        return redirect()->route('admin.almacenes.index')->with('success', 'Almacén creado correctamente.');
    }

    public function edit(Almacen $almacen)
    {
        return view('admin.almacenes.edit', compact('almacen'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        $almacen->update($request->all());

        return redirect()->route('admin.almacenes.index')->with('success', 'Almacén actualizado.');
    }

        public function showExistencia($id) {
        $almacen = Almacen::findOrFail($id);
        $existencias = Existencia::where('id_almacen', $id)->get();

        return view('admin.almacenes.existencia', compact('almacen', 'existencias'));
    }
}

