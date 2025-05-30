<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Insumo;
use Barryvdh\DomPDF\Facade\Pdf; // Usa la facade de DomPDF
use Illuminate\Support\Facades\Storage;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::query();

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        $cotizaciones = $query->latest()->paginate(10);

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $insumos = Insumo::where('id_tipo_insumo', '=', 2)->get();

        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        // Mano de obra
        $manoObra = Insumo::whereIn('nombre', [
            'Mano de Obra Cortina',
            'Mano de Obra Tergal'
        ])
            ->where('id_tipo_insumo', 3)
            ->get()
            ->keyBy('nombre');

        $telas = Insumo::where('id_tipo_insumo', 1)->get();

        $tergales = Insumo::where('id_tipo_insumo', 4)->get();

        $forros = Insumo::where('id_tipo_insumo', 5)->get();

        $cortineros = Insumo::where('id_tipo_insumo', 6)->get();

        return view('admin.cotizaciones.create', compact(
            'insumos',
            'insumosFijos',
            'manoObra',
            'telas',
            'tergales',
            'forros',
            'cortineros'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'fecha'              => 'required|date',
            'detalle'            => 'array',
            'totales'            => 'array',
        ], [
            'cliente_id.required' => 'El campo cliente es obligatorio.',
            'cliente_id.exists' => 'El cliente seleccionado no es válido.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'fecha.date' => 'La fecha debe ser válida.',
            'detalle.array' => 'El detalle debe ser un arreglo.',
            'totales.array' => 'Los totales deben ser un arreglo.',
        ]);

        $detalle = $request->input('detalle', []);
        $totales = $request->input('totales', []);

        $cotizacion = new Cotizacion();
        $cotizacion->cliente_id   = $validated['cliente_id'];
        $cotizacion->fecha        = $validated['fecha'];

        $tipos = $request->input('tipo', []);
        $cotizacion->lleva_cortina = in_array('cortina', $tipos);
        $cotizacion->lleva_tergal  = in_array('tergal', $tipos);
        $cotizacion->lleva_forro   = $request->has('lleva_forro');

        $cotizacion->total_lienzos     = $totales['total_lienzos'] ?? null;
        $cotizacion->total_m2_forro    = $totales['total_m2_forro'] ?? null;
        $cotizacion->total_m2_tela     = $totales['total_m2_tela'] ?? null;
        $cotizacion->total_m2_tergal   = $totales['total_m2_tergal'] ?? null;
        $cotizacion->costo_cortina     = $totales['costo_cortina'] ?? null;
        $cotizacion->utilidad          = $totales['utilidad'] ?? null;
        $cotizacion->costo_decorador   = $totales['costo_decorador'] ?? null;
        $cotizacion->precio_publico    = $totales['precio_publico'] ?? null;

        $cotizacion->estatus = $request->input('estatus', 'solicitada');

        $cotizacion->save();
        // Detalles de cortina
        if ($cotizacion->lleva_cortina && isset($detalle['ancho'])) {
            $cotizacion->detalleCotizacion()->create([
                'tela_id' => $detalle['tela_id'] ?? null,
                'ancho_tela' => $detalle['ancho_tela'] ?? null,
                'ancho' => $detalle['ancho'] ?? null,
                'largo' => $detalle['largo'] ?? null,
                'no_lienzos' => $detalle['no_lienzos'] ?? null,
                'no_lienzos_redondeado' => $detalle['no_lienzos_redondeado'] ?? null,
                'bastilla' => $detalle['valor_bastilla'] ?? null,
            ]);
        }

        $insumos = $request->input('insumos', []);
        if (!empty($insumos)) {
            $cotizacion->insumos()->attach($insumos);
        }

        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        $insumosAttach = [];

        foreach (['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'] as $nombre) {
            $insumo = $insumosFijos->get($nombre);
            if ($insumo) {
                $key = strtolower($nombre);
                $cantidad = $detalle["{$key}_cantidad"] ?? 0;
                $precio = $insumo->precio_publico;
                if ($cantidad > 0) {
                    $insumosAttach[$insumo->id] = [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $cantidad * $precio,
                    ];
                }
            }
        }

        foreach ($detalle as $k => $v) {
            if (preg_match('/^otros(\d+)_nombre$/', $k, $matches)) {
                $index = $matches[1];
                $insumoId = $v;
                $cantidad = $detalle["otros{$index}_cantidad"] ?? 0;
                $precio = $detalle["otros{$index}_precio"] ?? 0;
                if ($insumoId && $cantidad > 0) {
                    if (isset($insumosAttach[$insumoId])) {
                        $insumosAttach[$insumoId]['cantidad'] += $cantidad;
                        $insumosAttach[$insumoId]['subtotal'] += $cantidad * $precio;
                    } else {
                        $insumosAttach[$insumoId] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $cantidad * $precio,
                        ];
                    }
                }
            }
        }

        if (!empty($insumosAttach)) {
            $cotizacion->insumos()->attach($insumosAttach);
        }

        return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización creada exitosamente.');
    }

    public function show($id)
    {
        $cotizacion = Cotizacion::with(['cliente', 'insumos'])->findOrFail($id);
        return view('admin.cotizaciones.show', compact('cotizacion'));
    }

    public function edit($id)
    {
        $cotizacion = Cotizacion::with(['insumos', 'detalleCotizacion'])->findOrFail($id);
        $detalleCotizacion = $cotizacion->detalleCotizacion;

        // Obtener insumos para los selects y campos del formulario
        $insumos = Insumo::where('id_tipo_insumo', '=', 2)->get();

        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        $manoObra = Insumo::whereIn('nombre', [
            'Mano de Obra Cortina',
            'Mano de Obra Tergal'
        ])
            ->where('id_tipo_insumo', 3)
            ->get()
            ->keyBy('nombre');

        $telas = Insumo::where('id_tipo_insumo', 1)->get();
        $tergales = Insumo::where('id_tipo_insumo', 4)->get();
        $forros = Insumo::where('id_tipo_insumo', 5)->get();
        $cortineros = Insumo::where('id_tipo_insumo', 6)->get();

        return view('admin.cotizaciones.edit', compact(
            'cotizacion',
            'detalleCotizacion',
            'insumos',
            'insumosFijos',
            'manoObra',
            'telas',
            'tergales',
            'forros',
            'cortineros'
        ));
    }

    public function update(Request $request, $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'fecha'              => 'required|date',
            'detalle'            => 'array',
            'totales'            => 'array',
        ], [
            'cliente_id.required' => 'El campo cliente es obligatorio.',
            'cliente_id.exists' => 'El cliente seleccionado no es válido.',
            'fecha.required' => 'El campo fecha es obligatorio.',
            'fecha.date' => 'La fecha debe ser válida.',
            'detalle.array' => 'El detalle debe ser un arreglo.',
            'totales.array' => 'Los totales deben ser un arreglo.',
        ]);

        $detalle = $request->input('detalle', []);
        $totales = $request->input('totales', []);

        $cotizacion->cliente_id   = $validated['cliente_id'];
        $cotizacion->fecha        = $validated['fecha'];

        $tipos = $request->input('tipo', []);
        $cotizacion->lleva_cortina = in_array('cortina', $tipos);
        $cotizacion->lleva_tergal  = in_array('tergal', $tipos);
        $cotizacion->lleva_forro   = $request->has('lleva_forro');

        $cotizacion->total_lienzos     = $totales['total_lienzos'] ?? null;
        $cotizacion->total_m2_forro    = $totales['total_m2_forro'] ?? null;
        $cotizacion->total_m2_tela     = $totales['total_m2_tela'] ?? null;
        $cotizacion->total_m2_tergal   = $totales['total_m2_tergal'] ?? null;
        $cotizacion->costo_cortina     = $totales['costo_cortina'] ?? null;
        $cotizacion->utilidad          = $totales['utilidad'] ?? null;
        $cotizacion->costo_decorador   = $totales['costo_decorador'] ?? null;
        $cotizacion->precio_publico    = $totales['precio_publico'] ?? null;

        $cotizacion->estatus = $request->input('estatus', 'solicitada');

        $cotizacion->save();

        // Actualizar o crear detalle de cortina
        if ($cotizacion->lleva_cortina && isset($detalle['ancho'])) {
            $dataDetalle = [
                'tela_id' => $detalle['tela_id'] ?? null,
                'ancho_tela' => $detalle['ancho_tela'] ?? null,
                'ancho' => $detalle['ancho'] ?? null,
                'largo' => $detalle['largo'] ?? null,
                'no_lienzos' => $detalle['no_lienzos'] ?? null,
                'no_lienzos_redondeado' => $detalle['no_lienzos_redondeado'] ?? null,
                'bastilla' => $detalle['valor_bastilla'] ?? null,
            ];
            if ($cotizacion->detalleCotizacion) {
                $cotizacion->detalleCotizacion->update($dataDetalle);
            } else {
                $cotizacion->detalleCotizacion()->create($dataDetalle);
            }
        } else {
            // Si no lleva cortina, eliminar detalle si existe
            if ($cotizacion->detalleCotizacion) {
                $cotizacion->detalleCotizacion->delete();
            }
        }

        // Limpiar todos los insumos relacionados
        $cotizacion->insumos()->detach();

        // Adjuntar insumos seleccionados
        $insumos = $request->input('insumos', []);
        if (!empty($insumos)) {
            $cotizacion->insumos()->attach($insumos);
        }

        // Adjuntar insumos fijos y otros insumos con cantidades y precios personalizados
        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        $insumosAttach = [];

        foreach (['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'] as $nombre) {
            $insumo = $insumosFijos->get($nombre);
            if ($insumo) {
                $key = strtolower($nombre);
                $cantidad = $detalle["{$key}_cantidad"] ?? 0;
                $precio = $insumo->precio_publico;
                if ($cantidad > 0) {
                    $insumosAttach[$insumo->id] = [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $cantidad * $precio,
                    ];
                }
            }
        }

        foreach ($detalle as $k => $v) {
            if (preg_match('/^otros(\d+)_nombre$/', $k, $matches)) {
                $index = $matches[1];
                $insumoId = $v;
                $cantidad = $detalle["otros{$index}_cantidad"] ?? 0;
                $precio = $detalle["otros{$index}_precio"] ?? 0;
                if ($insumoId && $cantidad > 0) {
                    if (isset($insumosAttach[$insumoId])) {
                        $insumosAttach[$insumoId]['cantidad'] += $cantidad;
                        $insumosAttach[$insumoId]['subtotal'] += $cantidad * $precio;
                    } else {
                        $insumosAttach[$insumoId] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $cantidad * $precio,
                        ];
                    }
                }
            }
        }

        if (!empty($insumosAttach)) {
            $cotizacion->insumos()->attach($insumosAttach);
        }

        return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización actualizada exitosamente.');
    }

    public function destroy($id) {}

    public function cambiarEstatus(Request $request, Cotizacion $cotizacion)
    {
        $cotizacion->estatus = $request->estatus;
        $cotizacion->save();
        return redirect()->back()->with('success', 'Estatus actualizado correctamente.');
    }

    public function generarPdf($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        $pdf = Pdf::loadView('admin.cotizaciones.pdf', compact('cotizacion'));

        $fileName = 'cotizacion_' . $cotizacion->id . '.pdf';
        $filePath = 'pdfs/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());

        return response()->download(storage_path('app/public/pdfs/' . $fileName));
    }
}
