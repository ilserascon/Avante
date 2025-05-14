<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\Existencia;
use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;

class EntradaController extends Controller 
{
    public function index()
    {
        $entradas = Entrada::with(['almacen', 'usuario'])->get();
        return view('admin.entradas.index', compact('entradas'));
    }

    public function show($id)
    {
        $entrada = Entrada::with(['almacen', 'usuario', 'detalles'])->findOrFail($id);
        return view('admin.entradas.show', compact('entrada'));
    }

    public function create()
    {
        $almacenes = Almacen::all();
        $productos = Producto::all();
        $insumos = Insumo::all();
        return view('admin.entradas.create', compact('almacenes', 'productos', 'insumos'));
    }

    public function edit($id)
    {
        $entrada = Entrada::findOrFail($id); 
        $almacenes = Almacen::all(); 
        return view('admin.entradas.edit', compact('entrada', 'almacenes'));
    }

    public function update(Request $request, $id)
    {
        $entrada = Entrada::findOrFail($id);
        $entrada->update($request->all());
        return redirect()->route('admin.entradas.index')->with('success', 'Entrada actualizada correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_almacen' => 'required|exists:almacenes,id',
            'productos' => 'required|array',
            'productos.*.id_producto' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.01',
            'productos.*.precio_unitario' => 'required|numeric|min:0.01',
            'productos.*.id_insumo' => 'nullable|exists:insumo,id',
        ]);

        DB::transaction(function () use ($request) {
            $entrada = Entrada::create([
                'id_almacen' => $request->id_almacen,
                'id_usuario' => auth()->id(),
            ]);

            foreach ($request->productos as $producto) {
                DetalleEntrada::create([
                    'id_entrada' => $entrada->id,
                    'id_producto' => $producto['id_producto'],
                    'id_insumo' => $producto['id_insumo'] ?? null,
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio_unitario'],
                ]);
            }
        });

        return redirect()->route('admin.entradas.index')->with('success', 'Entrada creada correctamente.');
    }
}
