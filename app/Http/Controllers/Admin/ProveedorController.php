<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->has('nombre') && $request->nombre != '') {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        if ($request->has('rfc') && $request->rfc != '') {
            $query->where('rfc', 'LIKE', '%' . $request->rfc . '%');
        }

        $proveedores = $query->where('borrado', 0)->paginate(10);

        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('admin.proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|max:255|unique:proveedores',
            'razon_social' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:proveedores',
        ]);
    
        Proveedor::create($validated + ['borrado' => 0]);
    
        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor creado exitosamente');
    }
    

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|max:255|unique:proveedores,rfc,' . $proveedor->id,
            'razon_social' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:proveedores,email,' . $proveedor->id,
        ]);

        $proveedor->update($validated);

        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update(['borrado' => 1]);

        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor eliminado exitosamente');
    }
}
