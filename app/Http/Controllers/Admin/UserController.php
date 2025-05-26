<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        $estado = $request->get('estado', 'habilitado');
        if ($estado === 'habilitado') {
            $query->where('borrado', 0);
        } elseif ($estado === 'inhabilitado') {
            $query->where('borrado', 1);
        }

        $users = $query->get();
        return view('admin.users.index', compact('users', 'estado'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|min:6'
        ], [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'role_id.required' => 'El campo rol es obligatorio.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $roles = Role::all();
        $user = User::findOrFail($id); 
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id); 

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6'
        ], [
            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'role_id.required' => 'El campo rol es obligatorio.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $data = $request->only(['name', 'email', 'role_id']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id); 
        $user->update(['borrado' => 1]);
        return redirect()->route('admin.users.index')->with('success', 'Usuario inhabilitado.');
    }

    public function habilitar($id)
    {
        $user = User::findOrFail($id);
        $user->update(['borrado' => 0]);
        return redirect()->route('admin.users.index', ['estado' => 'inhabilitado'])->with('success', 'Usuario habilitado.');
    }
}
