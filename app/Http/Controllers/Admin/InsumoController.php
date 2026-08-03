<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\InsumosImport;
use App\Imports\InsumosImportReader;
use App\Models\Insumo;
use App\Models\TipoInsumo;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    public function index(Request $request)
    {
        $tipos = TipoInsumo::orderBy('nombre')->get();
        $tipoSeleccionado = $request->get('tipo_insumo');

        $query = Insumo::with(['tipoInsumo', 'proveedor']);

        if ($request->has('nombre') && $request->nombre != '') {
            $termino = $request->nombre;
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'LIKE', '%' . $termino . '%')
                    ->orWhere('clave', 'LIKE', '%' . $termino . '%');
            });
        }

        $camposDinamicos = [];

        if ($tipoSeleccionado) {
            $tipo = TipoInsumo::find($tipoSeleccionado);

            if ($tipo) {
                foreach ($tipo->getAttributes() as $campo => $valor) {
                    if (str_starts_with($campo, 'campo') && !empty($valor)) {
                        $camposDinamicos[$campo] = $valor;
                    }
                }
            }

            $query->where('id_tipo_insumo', $tipoSeleccionado);
        }

        $estado = $request->get('estado', 'habilitado');
        if ($estado === 'habilitado') {
            $query->where('borrado', 0);
        } elseif ($estado === 'inhabilitado') {
            $query->where('borrado', 1);
        }

        $insumos = $query->paginate(10)->appends($request->query());

        return view('admin.insumos.index', compact('insumos', 'tipos', 'tipoSeleccionado', 'camposDinamicos', 'estado'));
    }

    public function create()
    {
        $proveedores = Proveedor::all();
        $tiposInsumo = $this->tiposInsumoParaFormulario();

        return view('admin.insumos.create', compact('proveedores', 'tiposInsumo'));
    }

    public function store(Request $request)
    {
        $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false;

        $rules = [
            'nombre' => 'required|string|max:255',
            'clave' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'id_proveedor' => 'required|exists:proveedores,id',
            'id_tipo_insumo' => 'required|exists:tipo_insumo,id',
            'precio_publico' => 'required|numeric',
            'campo1' => 'nullable|string',
            'campo2' => 'nullable|string',
            'campo3' => 'nullable|string',
            'campo4' => 'nullable|string',
            'campo5' => 'nullable|string',
            'campo6' => 'nullable|string',
            'campo7' => 'nullable|string',
            'campo8' => 'nullable|string',
            'campo9' => 'nullable|string',
            'campo10' => 'nullable|string',
            'campo11' => 'nullable|string',
            'campo12' => 'nullable|string',
            'campo13' => 'nullable|string',
            'campo14' => 'nullable|string',
            'campo15' => 'nullable|string',
        ];

        if ($veCostos) {
            $rules['costo'] = 'required|numeric';
            $rules['utilidad'] = 'required|numeric';
        }

        $validatedData = $request->validate($rules, [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'id_proveedor.required' => 'El campo proveedor es obligatorio.',
            'id_proveedor.exists' => 'El proveedor seleccionado no es válido.',
            'id_tipo_insumo.required' => 'El campo tipo de insumo es obligatorio.',
            'id_tipo_insumo.exists' => 'El tipo de insumo seleccionado no es válido.',
            'costo.required' => 'El campo costo es obligatorio.',
            'costo.numeric' => 'El campo costo debe ser numérico.',
            'precio_publico.required' => 'El campo precio público es obligatorio.',
            'precio_publico.numeric' => 'El campo precio público debe ser numérico.',
            'utilidad.required' => 'El campo utilidad es obligatorio.',
            'utilidad.numeric' => 'El campo utilidad debe ser numérico.',
        ]);

        $insumo = new Insumo();
        $insumo->nombre = $request->nombre;
        $insumo->clave = $request->clave;
        $insumo->color = $request->color;
        $insumo->id_proveedor = $request->id_proveedor;
        $insumo->id_tipo_insumo = $request->id_tipo_insumo;
        $insumo->costo = $veCostos ? $request->costo : null;
        $insumo->precio_publico = $request->precio_publico;
        $insumo->utilidad = $veCostos ? $request->utilidad : null;

        for ($i = 1; $i <= 15; $i++) {
            $campo = 'campo' . $i;
            $insumo->$campo = $request->$campo ?? null;
        }

        $insumo->borrado = 0;
        $insumo->save();
        
        return redirect()->route('admin.insumos.index')->with('success', 'Insumo creado exitosamente');
    }

    public function show($id)
    {
        $insumo = Insumo::with(['tipoInsumo', 'proveedor'])->findOrFail($id);
        $camposDinamicos = [];

        if ($insumo->tipoInsumo) {
            foreach ($insumo->tipoInsumo->getAttributes() as $campo => $valor) {
                if (str_starts_with($campo, 'campo') && !empty($valor)) {
                    $camposDinamicos[$campo] = $valor;
                }
            }
        }

        return view('admin.insumos.show', compact('insumo', 'camposDinamicos'));
    }

    public function edit($id)
    {
        $insumo = Insumo::findOrFail($id);
        $proveedores = Proveedor::all();
        $tiposInsumo = $this->tiposInsumoParaFormulario();

        return view('admin.insumos.edit', compact('insumo', 'proveedores', 'tiposInsumo'));
    }

    public function update(Request $request, Insumo $insumo)
    {
        $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false;

        $validated = $request->validate([
            'nombre'           => 'required|string|max:255',
            'clave'            => 'nullable|string|max:255',
            'color'            => 'nullable|string|max:255',
            'id_tipo_insumo'   => 'required|exists:tipo_insumo,id',
            'id_proveedor'     => 'required|exists:proveedores,id',
            'costo'            => 'nullable|numeric',
            'precio_publico'   => 'nullable|numeric',
            'utilidad'         => 'nullable|numeric',
            'campo1'           => 'nullable|string|max:255',
            'campo2'           => 'nullable|string|max:255',
            'campo3'           => 'nullable|string|max:255',
            'campo4'           => 'nullable|string|max:255',
            'campo5'           => 'nullable|string|max:255',
            'campo6'           => 'nullable|string|max:255',
            'campo7'           => 'nullable|string|max:255',
            'campo8'           => 'nullable|string|max:255',
            'campo9'           => 'nullable|string|max:255',
            'campo10'          => 'nullable|string|max:255',
            'campo11'          => 'nullable|string|max:255',
            'campo12'          => 'nullable|string|max:255',
            'campo13'          => 'nullable|string|max:255',
            'campo14'          => 'nullable|string|max:255',
            'campo15'          => 'nullable|string|max:255',
        ], [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El campo nombre no debe exceder 255 caracteres.',
            'id_proveedor.required' => 'El campo proveedor es obligatorio.',
            'id_proveedor.exists' => 'El proveedor seleccionado no es válido.',
            'id_tipo_insumo.required' => 'El campo tipo de insumo es obligatorio.',
            'id_tipo_insumo.exists' => 'El tipo de insumo seleccionado no es válido.',
            'costo.numeric' => 'El campo costo debe ser numérico.',
            'precio_publico.numeric' => 'El campo precio público debe ser numérico.',
            'utilidad.numeric' => 'El campo utilidad debe ser numérico.',
        ]);

        if (!$veCostos) {
            unset($validated['costo'], $validated['utilidad']);
        }

        $insumo->update($validated);

        return redirect()->route('admin.insumos.index')->with('success', 'Insumo actualizado correctamente');
    }

    public function camposDinamicosPorTipo(Request $request)
    {
        $tipo = TipoInsumo::find($request->id_tipo_insumo);
        $campos = [];
        if ($tipo) {
            for ($i = 1; $i <= 15; $i++) {
                $campo = 'campo' . $i;
                if (!empty($tipo->$campo)) {
                    $campos[$campo] = $tipo->$campo;
                }
            }
        }
        return response()->json($campos);
    }

    public function import(Request $request)
    {
        $request->validate([
            'id_tipo_insumo' => 'required|exists:tipo_insumo,id',
            'archivo' => 'required|file|mimes:xlsx,csv,xls,xml,txt',
        ]);

        $import = new InsumosImport((int) $request->id_tipo_insumo);

        try {
            $filas = InsumosImportReader::leerArchivo($request->file('archivo'));
            $import->procesarFilas($filas);
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.insumos.index', ['tipo_insumo' => $request->id_tipo_insumo])
                ->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'beyond highest row')) {
                return redirect()
                    ->route('admin.insumos.index', ['tipo_insumo' => $request->id_tipo_insumo])
                    ->with(
                        'error',
                        'No se pudieron leer filas de datos del archivo. Guarde el archivo como Excel (.xlsx) o CSV e intente de nuevo.'
                    );
            }

            return redirect()
                ->route('admin.insumos.index', ['tipo_insumo' => $request->id_tipo_insumo])
                ->with('error', 'No se pudo leer el archivo. Guarde el archivo como Excel (.xlsx) e intente de nuevo.');
        }

        $resumen = $import->getResumen();
        $mensaje = sprintf(
            'Importación completada: %d creado(s), %d actualizado(s)%s.',
            $resumen['creados'],
            $resumen['actualizados'],
            $resumen['omitidos'] > 0 ? ', ' . $resumen['omitidos'] . ' omitido(s)' : ''
        );

        return redirect()
            ->route('admin.insumos.index', ['tipo_insumo' => $request->id_tipo_insumo])
            ->with('success', $mensaje);
    }

    public function destroy($id)
    {
        $insumo = insumo::findOrFail($id);
        $insumo->update(['borrado' => 1]);
        return redirect()->route('admin.insumos.index')->with('success', 'insumo inhabilitado exitosamente');
    }

    public function habilitar($id)
    {
        $insumo = insumo::findOrFail($id);
        $insumo->update(['borrado' => 0]);
        return redirect()->route('admin.insumos.index', ['estado' => 'inhabilitado'])->with('success', 'insumo habilitado exitosamente');
    }

    private function tiposInsumoParaFormulario()
    {
        return TipoInsumo::orderBy('nombre')->get()->map(function ($tipo) {
            $campos = [];
            for ($i = 1; $i <= 15; $i++) {
                $campo = 'campo' . $i;
                if (!empty($tipo->$campo)) {
                    $campos[$campo] = $tipo->$campo;
                }
            }
            $tipo->campos_data = $campos;

            return $tipo;
        });
    }
}
