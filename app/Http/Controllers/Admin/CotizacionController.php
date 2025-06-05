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

        $tipos = array_filter($request->input('tipo', []), function($v) {
            return $v !== '__dummy_cortina__' && $v !== '__dummy_tergal__';
        });
        $cotizacion->lleva_cortina = in_array('cortina', $tipos);
        $cotizacion->lleva_tergal  = in_array('tergal', $tipos);
        $cotizacion->lleva_forro   = $request->input('lleva_forro', 0) == 1;

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

        $dataDetalle = [];

        // Datos de cortina
        if ($cotizacion->lleva_cortina && isset($detalle['ancho'])) {
            $dataDetalle['tela_id'] = $detalle['tela_id'] ?? null;
            $dataDetalle['ancho_tela'] = $detalle['ancho_tela'] ?? null;
            $dataDetalle['ancho'] = $detalle['ancho'] ?? null;
            $dataDetalle['largo'] = $detalle['largo'] ?? null;
            $dataDetalle['no_lienzos'] = $detalle['no_lienzos'] ?? null;
            $dataDetalle['no_lienzos_redondeado'] = $detalle['no_lienzos_redondeado'] ?? null;
            $dataDetalle['bastilla'] = $detalle['valor_bastilla'] ?? null;
        }

        // Datos de tergal
        if ($cotizacion->lleva_tergal && isset($detalle['ancho_tergal'])) {
            $dataDetalle['tergal_id'] = $detalle['tergal_id'] ?? null;
            $dataDetalle['ancho_tergal'] = $detalle['ancho_tergal'] ?? null;
            $dataDetalle['ancho_tergal_real'] = $detalle['ancho_tergal_real'] ?? null;
            $dataDetalle['largo_tergal'] = $detalle['largo_tergal'] ?? null;
            $dataDetalle['no_lienzos_tergal'] = $detalle['no_lienzos_tergal'] ?? null;
            $dataDetalle['no_lienzos_redondeado_tergal'] = $detalle['no_lienzos_redondeado_tergal'] ?? null;
            $dataDetalle['bastilla_tergal'] = $detalle['valor_bastilla_tergal'] ?? null;
        }

        // Datos de forro
        if ($cotizacion->lleva_forro && isset($detalle['ancho_forro'])) {
            $dataDetalle['forro_id'] = $detalle['forro_id'] ?? null;
            $dataDetalle['ancho_forro'] = $detalle['ancho_forro'] ?? null;
            $dataDetalle['ancho_forro_real'] = $detalle['ancho_forro_real'] ?? null;
            $dataDetalle['largo_forro'] = $detalle['largo_forro'] ?? null;
            $dataDetalle['no_lienzos_forro'] = $detalle['no_lienzos_forro'] ?? null;
            $dataDetalle['no_lienzos_redondeado_forro'] = $detalle['no_lienzos_redondeado_forro'] ?? null;
            $dataDetalle['bastilla_forro'] = $detalle['valor_bastilla_forro'] ?? null;
        }

        // Datos de la tabla "Total Tela, Tergal y Forro"
        $dataDetalle['total_tela'] = $detalle['total_tela'] ?? null;
        $dataDetalle['precio_m2_tela'] = $detalle['precio_m2_tela'] ?? null;
        $dataDetalle['descripcion_tela'] = $detalle['descripcion_tela'] ?? null;
        $dataDetalle['total_tela_final'] = $detalle['total_tela_final'] ?? null;

        $dataDetalle['total_tergal'] = $detalle['total_tergal'] ?? null;
        $dataDetalle['precio_m2_tergal'] = $detalle['precio_m2_tergal'] ?? null;
        $dataDetalle['descripcion_tergal'] = $detalle['descripcion_tergal'] ?? null;
        $dataDetalle['total_tergal_final'] = $detalle['total_tergal_final'] ?? null;

        $dataDetalle['total_forro'] = $detalle['total_forro'] ?? null;
        $dataDetalle['precio_m2_forro'] = $detalle['precio_m2_forro'] ?? null;
        $dataDetalle['descripcion_forro'] = $detalle['descripcion_forro'] ?? null;
        $dataDetalle['total_final_forro'] = $detalle['total_final_forro'] ?? null;

        $dataDetalle['costo_total_tela_tergal_forro'] = $detalle['costo_total_tela_tergal_forro'] ?? null;

        // Datos de la tabla "Mano de Obra"
        $dataDetalle['m2_1'] = $detalle['m2_1'] ?? null;
        $dataDetalle['costo_mano_obra_1'] = $detalle['costo_mano_obra_1'] ?? null;
        $dataDetalle['total_mano_obra_1'] = $detalle['total_mano_obra_1'] ?? null;

        $dataDetalle['m2_2'] = $detalle['m2_2'] ?? null;
        $dataDetalle['costo_mano_obra_2'] = $detalle['costo_mano_obra_2'] ?? null;
        $dataDetalle['total_mano_obra_2'] = $detalle['total_mano_obra_2'] ?? null;

        $dataDetalle['costo_total_mano_obra'] = $detalle['costo_total_mano_obra'] ?? null;

        if (!empty($dataDetalle)) {
            $cotizacion->detalleCotizacion()->create($dataDetalle);
        }

        // ===== LÓGICA CORREGIDA PARA MANEJO DE INSUMOS =====

        // 1. Limpiar todos los insumos relacionados primero
        $cotizacion->insumos()->detach();

        // 2. Preparar array para todos los insumos que se van a adjuntar
        $todosLosInsumos = [];

        // 3. Adjuntar insumos seleccionados básicos (sin cantidad/precio personalizado)
        $insumosBasicos = $request->input('insumos', []);
        foreach ($insumosBasicos as $insumoId) {
            $todosLosInsumos[$insumoId] = [
                'cantidad' => 1,
                'precio_unitario' => 0,
                'subtotal' => 0,
            ];
        }

        // 4. Obtener insumos fijos para procesar cantidades y precios
        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        // 5. Procesar insumos fijos con cantidades y precios
        foreach (['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'] as $nombre) {
            $insumo = $insumosFijos->get($nombre);
            if ($insumo) {
                $key = strtolower($nombre);
                $cantidad = $detalle["{$key}_cantidad"] ?? 0;
                $precio = $insumo->precio_publico;

                if ($cantidad > 0) {
                    $subtotal = $cantidad * $precio;

                    // Si ya existe en el array, actualizar; si no, agregar
                    if (isset($todosLosInsumos[$insumo->id])) {
                        $todosLosInsumos[$insumo->id]['cantidad'] += $cantidad;
                        $todosLosInsumos[$insumo->id]['precio_unitario'] = $precio;
                        $todosLosInsumos[$insumo->id]['subtotal'] += $subtotal;
                    } else {
                        $todosLosInsumos[$insumo->id] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $subtotal,
                        ];
                    }
                }
            }
        }

        // 6. Procesar "otros insumos" dinámicos
        foreach ($detalle as $key => $value) {
            if (preg_match('/^otros(\d+)_nombre$/', $key, $matches)) {
                $index = $matches[1];
                $insumoId = $value;
                $cantidad = $detalle["otros{$index}_cantidad"] ?? 0;
                $precio = $detalle["otros{$index}_precio"] ?? 0;

                if ($insumoId && $cantidad > 0 && $precio > 0) {
                    $subtotal = $cantidad * $precio;

                    // Si ya existe en el array, actualizar; si no, agregar
                    if (isset($todosLosInsumos[$insumoId])) {
                        $todosLosInsumos[$insumoId]['cantidad'] += $cantidad;
                        $todosLosInsumos[$insumoId]['subtotal'] += $subtotal;
                        // Para "otros insumos", mantenemos el precio que se ingresó
                        $todosLosInsumos[$insumoId]['precio_unitario'] = $precio;
                    } else {
                        $todosLosInsumos[$insumoId] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $subtotal,
                        ];
                    }
                }
            }
        }

        // 7. Adjuntar todos los insumos de una sola vez
        if (!empty($todosLosInsumos)) {
            $cotizacion->insumos()->attach($todosLosInsumos);
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

        // Permite que detalle y totales sean opcionales
        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'fecha'              => 'required|date',
            'detalle'            => 'nullable|array',
            'totales'            => 'nullable|array',
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

        $tipos = $request->input('tipo', []);
        $cotizacion->cliente_id   = $validated['cliente_id'];
        $cotizacion->fecha        = $validated['fecha'];
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

        $detalleCotizacion = $cotizacion->detalleCotizacion;
        if (!$detalleCotizacion) {
            $detalleCotizacion = $cotizacion->detalleCotizacion()->create([]);
        }

        $dataDetalle = [];

        // CORTINA
        if ($cotizacion->lleva_cortina) {
            $dataDetalle['tela_id'] = $detalle['tela_id'] ?? null;
            $dataDetalle['ancho_tela'] = $detalle['ancho_tela'] ?? null;
            $dataDetalle['ancho'] = $detalle['ancho'] ?? null;
            $dataDetalle['largo'] = $detalle['largo'] ?? null;
            $dataDetalle['no_lienzos'] = $detalle['no_lienzos'] ?? null;
            $dataDetalle['no_lienzos_redondeado'] = $detalle['no_lienzos_redondeado'] ?? null;
            $dataDetalle['bastilla'] = $detalle['valor_bastilla'] ?? null;
            $dataDetalle['total_tela'] = $detalle['total_tela'] ?? null;
            $dataDetalle['precio_m2_tela'] = $detalle['precio_m2_tela'] ?? null;
            $dataDetalle['descripcion_tela'] = $detalle['descripcion_tela'] ?? null;
            $dataDetalle['total_tela_final'] = $detalle['total_tela_final'] ?? null;
            $dataDetalle['m2_1'] = $detalle['m2_1'] ?? null;
            $dataDetalle['costo_mano_obra_1'] = $detalle['costo_mano_obra_1'] ?? null;
            $dataDetalle['total_mano_obra_1'] = $detalle['total_mano_obra_1'] ?? null;
        } else {
            $dataDetalle['tela_id'] = null;
            $dataDetalle['ancho_tela'] = null;
            $dataDetalle['ancho'] = null;
            $dataDetalle['largo'] = null;
            $dataDetalle['no_lienzos'] = null;
            $dataDetalle['no_lienzos_redondeado'] = null;
            $dataDetalle['bastilla'] = null;
            $dataDetalle['total_tela'] = null;
            $dataDetalle['precio_m2_tela'] = null;
            $dataDetalle['descripcion_tela'] = null;
            $dataDetalle['total_tela_final'] = null;
            $dataDetalle['m2_1'] = null;
            $dataDetalle['costo_mano_obra_1'] = null;
            $dataDetalle['total_mano_obra_1'] = null;
        }

        // TERGAL
        if ($cotizacion->lleva_tergal) {
            $dataDetalle['tergal_id'] = $detalle['tergal_id'] ?? null;
            $dataDetalle['ancho_tergal'] = $detalle['ancho_tergal'] ?? null;
            $dataDetalle['ancho_tergal_real'] = $detalle['ancho_tergal_real'] ?? null;
            $dataDetalle['largo_tergal'] = $detalle['largo_tergal'] ?? null;
            $dataDetalle['no_lienzos_tergal'] = $detalle['no_lienzos_tergal'] ?? null;
            $dataDetalle['no_lienzos_redondeado_tergal'] = $detalle['no_lienzos_redondeado_tergal'] ?? null;
            $dataDetalle['bastilla_tergal'] = $detalle['valor_bastilla_tergal'] ?? null;
            $dataDetalle['total_tergal'] = $detalle['total_tergal'] ?? null;
            $dataDetalle['precio_m2_tergal'] = $detalle['precio_m2_tergal'] ?? null;
            $dataDetalle['descripcion_tergal'] = $detalle['descripcion_tergal'] ?? null;
            $dataDetalle['total_tergal_final'] = $detalle['total_tergal_final'] ?? null;
            $dataDetalle['m2_2'] = $detalle['m2_2'] ?? null;
            $dataDetalle['costo_mano_obra_2'] = $detalle['costo_mano_obra_2'] ?? null;
            $dataDetalle['total_mano_obra_2'] = $detalle['total_mano_obra_2'] ?? null;
        } else {
            $dataDetalle['tergal_id'] = null;
            $dataDetalle['ancho_tergal'] = null;
            $dataDetalle['ancho_tergal_real'] = null;
            $dataDetalle['largo_tergal'] = null;
            $dataDetalle['no_lienzos_tergal'] = null;
            $dataDetalle['no_lienzos_redondeado_tergal'] = null;
            $dataDetalle['bastilla_tergal'] = null;
            $dataDetalle['total_tergal'] = null;
            $dataDetalle['precio_m2_tergal'] = null;
            $dataDetalle['descripcion_tergal'] = null;
            $dataDetalle['total_tergal_final'] = null;
            $dataDetalle['m2_2'] = null;
            $dataDetalle['costo_mano_obra_2'] = null;
            $dataDetalle['total_mano_obra_2'] = null;
        }

        // FORRO
        if ($cotizacion->lleva_forro) {
            $dataDetalle['forro_id'] = $detalle['forro_id'] ?? null;
            $dataDetalle['ancho_forro'] = $detalle['ancho_forro'] ?? null;
            $dataDetalle['ancho_forro_real'] = $detalle['ancho_forro_real'] ?? null;
            $dataDetalle['largo_forro'] = $detalle['largo_forro'] ?? null;
            $dataDetalle['no_lienzos_forro'] = $detalle['no_lienzos_forro'] ?? null;
            $dataDetalle['no_lienzos_redondeado_forro'] = $detalle['no_lienzos_redondeado_forro'] ?? null;
            $dataDetalle['bastilla_forro'] = $detalle['valor_bastilla_forro'] ?? null;
            $dataDetalle['total_forro'] = $detalle['total_forro'] ?? null;
            $dataDetalle['precio_m2_forro'] = $detalle['precio_m2_forro'] ?? null;
            $dataDetalle['descripcion_forro'] = $detalle['descripcion_forro'] ?? null;
            $dataDetalle['total_final_forro'] = $detalle['total_final_forro'] ?? null;
        } else {
            $dataDetalle['forro_id'] = null;
            $dataDetalle['ancho_forro'] = null;
            $dataDetalle['ancho_forro_real'] = null;
            $dataDetalle['largo_forro'] = null;
            $dataDetalle['no_lienzos_forro'] = null;
            $dataDetalle['no_lienzos_redondeado_forro'] = null;
            $dataDetalle['bastilla_forro'] = null;
            $dataDetalle['total_forro'] = null;
            $dataDetalle['precio_m2_forro'] = null;
            $dataDetalle['descripcion_forro'] = null;
            $dataDetalle['total_final_forro'] = null;
        }

        $dataDetalle['costo_total_tela_tergal_forro'] = $detalle['costo_total_tela_tergal_forro'] ?? null;
        $dataDetalle['costo_total_mano_obra'] = $detalle['costo_total_mano_obra'] ?? null;

        $detalleCotizacion->update($dataDetalle);

        // Insumos (igual que antes)
        $todosLosInsumos = [];
        $insumosSeleccionados = $request->input('insumos', []);
        foreach ($insumosSeleccionados as $insumoId) {
            if (!isset($todosLosInsumos[$insumoId])) {
                $todosLosInsumos[$insumoId] = [
                    'cantidad' => 1,
                    'precio_unitario' => 0,
                    'subtotal' => 0,
                ];
            }
        }

        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        foreach (['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'] as $nombre) {
            $insumo = $insumosFijos->get($nombre);
            if ($insumo) {
                $key = strtolower($nombre);
                $cantidad = $detalle["{$key}_cantidad"] ?? 0;

                if ($nombre === 'Cortinero') {
                    $cortineroId = $detalle['cortinero_id'] ?? null;
                    $precio = $detalle['cortinero_precio'] ?? 0;
                    if ($cortineroId && $cantidad > 0) {
                        $todosLosInsumos[$cortineroId] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $cantidad * $precio,
                        ];
                    }
                    continue;
                }

                $precio = $insumo->precio_publico;
                if ($cantidad > 0) {
                    $todosLosInsumos[$insumo->id] = [
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
                    if (isset($todosLosInsumos[$insumoId])) {
                        $todosLosInsumos[$insumoId]['cantidad'] += $cantidad;
                        $todosLosInsumos[$insumoId]['subtotal'] += $cantidad * $precio;
                    } else {
                        $todosLosInsumos[$insumoId] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $cantidad * $precio,
                        ];
                    }
                }
            }
        }

        if (!empty($todosLosInsumos)) {
            $cotizacion->insumos()->sync($todosLosInsumos);
        } else {
            $cotizacion->insumos()->detach();
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
