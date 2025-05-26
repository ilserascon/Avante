<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();
    
        if ($request->has('nombre') && $request->nombre != '') {
            $query->where('nombre', 'LIKE', '%' . $request->nombre . '%');
        }

        if ($request->has('rfc') && $request->rfc != '') {
            $query->where('rfc', 'LIKE', '%' . $request->rfc . '%');
        }

        $clientes = $query->where('borrado', 0)->paginate(10);

        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|max:255|unique:clientes',
            'razon_social' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes',
            'direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'rfc.required' => 'El campo RFC es obligatorio.',
            'rfc.unique' => 'El RFC ya está registrado.',
            'rfc.max' => 'El campo RFC no debe exceder 255 caracteres.',
            'razon_social.required' => 'El campo razón social es obligatorio.',
            'razon_social.max' => 'El campo razón social no debe exceder 255 caracteres.',
            'telefono.max' => 'El campo teléfono no debe exceder 255 caracteres.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo electrónico no debe exceder 255 caracteres.',
            'direccion.max' => 'El campo dirección no debe exceder 255 caracteres.',
            'codigo_postal.max' => 'El campo código postal no debe exceder 10 caracteres.',
        ]);

        Cliente::create($validated + ['borrado' => 0]);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente creado exitosamente');
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'required|string|max:255|unique:clientes,rfc,' . $cliente->id,
            'razon_social' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'direccion' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:10',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'rfc.required' => 'El campo RFC es obligatorio.',
            'rfc.unique' => 'El RFC ya está registrado.',
            'rfc.max' => 'El campo RFC no debe exceder 255 caracteres.',
            'razon_social.required' => 'El campo razón social es obligatorio.',
            'razon_social.max' => 'El campo razón social no debe exceder 255 caracteres.',
            'telefono.max' => 'El campo teléfono no debe exceder 255 caracteres.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'email.max' => 'El correo electrónico no debe exceder 255 caracteres.',
            'direccion.max' => 'El campo dirección no debe exceder 255 caracteres.',
            'codigo_postal.max' => 'El campo código postal no debe exceder 10 caracteres.',
        ]);

        $cliente->update($validated);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update(['borrado' => 1]);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente eliminado exitosamente');
    }
}