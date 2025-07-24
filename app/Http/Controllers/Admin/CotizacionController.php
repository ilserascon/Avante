<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Insumo;
use Barryvdh\DomPDF\Facade\Pdf; // Usa la facade de DomPDF
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $insumos = Insumo::whereIn('id_tipo_insumo', [2, 7])->get();

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
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'detalle' => 'array',
            'totales' => 'array',
        ]);

        $detalle = $request->input('detalle', []);
        $totales = $request->input('totales', []);

        $cotizacion = new Cotizacion();
        $cotizacion->cliente_id = $validated['cliente_id'];
        $cotizacion->fecha = $validated['fecha'];

        $tipos = array_filter($request->input('tipo', []), function ($v) {
            return $v !== '__dummy_cortina__' && $v !== '__dummy_tergal__';
        });
        $cotizacion->lleva_cortina = in_array('cortina', $tipos);
        $cotizacion->lleva_tergal = in_array('tergal', $tipos);
        $cotizacion->lleva_forro = $request->input('lleva_forro', 0) == 1;

        $cotizacion->total_lienzos = $totales['total_lienzos'] ?? null;
        $cotizacion->total_m2_forro = $totales['total_m2_forro'] ?? null;
        $cotizacion->total_m2_tela = $totales['total_m2_tela'] ?? null;
        $cotizacion->total_m2_tergal = $totales['total_m2_tergal'] ?? null;
        $cotizacion->costo_cortina = $totales['costo_cortina'] ?? null;
        $cotizacion->utilidad = $totales['utilidad'] ?? null;
        $cotizacion->costo_decorador = $totales['costo_decorador'] ?? null;
        $cotizacion->precio_publico = $totales['precio_publico'] ?? null;
        $cotizacion->aplicar_iva = $request->has('aplicar_iva');
        $cotizacion->descuento = $request->input('descuento', 0);
        $cotizacion->estatus = $request->input('estatus', 'solicitada');

        $cotizacion->save();

        $dataDetalle = [];

        // Cortina
        if ($cotizacion->lleva_cortina && isset($detalle['ancho'])) {
            $dataDetalle['tela_id'] = $detalle['tela_id'] ?? null;
            $dataDetalle['ancho_tela'] = $detalle['ancho_tela'] ?? null;
            $dataDetalle['ancho'] = $detalle['ancho'] ?? null;
            $dataDetalle['largo'] = $detalle['largo'] ?? null;
            $dataDetalle['no_lienzos'] = $detalle['no_lienzos'] ?? null;
            $dataDetalle['no_lienzos_redondeado'] = $detalle['no_lienzos_redondeado'] ?? null;
            $dataDetalle['bastilla'] = $detalle['valor_bastilla'] ?? null;
            $dataDetalle['tipo_cortina'] = $detalle['tipo_cortina'] ?? null;
        }

        // Tergal
        if ($cotizacion->lleva_tergal && isset($detalle['ancho_tergal'])) {
            $dataDetalle['tergal_id'] = $detalle['tergal_id'] ?? null;
            $dataDetalle['ancho_tergal'] = $detalle['ancho_tergal'] ?? null;
            $dataDetalle['ancho_tergal_real'] = $detalle['ancho_tergal_real'] ?? null;
            $dataDetalle['largo_tergal'] = $detalle['largo_tergal'] ?? null;
            $dataDetalle['no_lienzos_tergal'] = $detalle['no_lienzos_tergal'] ?? null;
            $dataDetalle['no_lienzos_redondeado_tergal'] = $detalle['no_lienzos_redondeado_tergal'] ?? null;
            $dataDetalle['bastilla_tergal'] = $detalle['valor_bastilla_tergal'] ?? null;
        }

        // Forro
        if ($cotizacion->lleva_forro && isset($detalle['ancho_forro'])) {
            $dataDetalle['forro_id'] = $detalle['forro_id'] ?? null;
            $dataDetalle['ancho_forro'] = $detalle['ancho_forro'] ?? null;
            $dataDetalle['ancho_forro_real'] = $detalle['ancho_forro_real'] ?? null;
            $dataDetalle['largo_forro'] = $detalle['largo_forro'] ?? null;
            $dataDetalle['no_lienzos_forro'] = $detalle['no_lienzos_forro'] ?? null;
            $dataDetalle['no_lienzos_redondeado_forro'] = $detalle['no_lienzos_redondeado_forro'] ?? null;
            $dataDetalle['bastilla_forro'] = $detalle['valor_bastilla_forro'] ?? null;
        }

        // Totales
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

        // Mano de obra
        $dataDetalle['m2_1'] = $detalle['m2_1'] ?? null;
        $dataDetalle['costo_mano_obra_1'] = $detalle['costo_mano_obra_1'] ?? null;
        $dataDetalle['total_mano_obra_1'] = $detalle['total_mano_obra_1'] ?? null;

        $dataDetalle['m2_2'] = $detalle['m2_2'] ?? null;
        $dataDetalle['costo_mano_obra_2'] = $detalle['costo_mano_obra_2'] ?? null;
        $dataDetalle['total_mano_obra_2'] = $detalle['total_mano_obra_2'] ?? null;

        $dataDetalle['costo_total_mano_obra'] = $detalle['costo_total_mano_obra'] ?? null;
        $dataDetalle['decorador_porcentaje'] = $totales['decorador_porcentaje'] ?? null;

        // Cortineros
        $dataDetalle['cortinero_id'] = $detalle['cortinero_id'] ?? null;
        $dataDetalle['cortinero_cantidad'] = $detalle['cortinero_cantidad'] ?? null;
        $dataDetalle['cortinero_precio'] = $detalle['cortinero_precio'] ?? null;
        $dataDetalle['cortinero_tergal_id'] = $detalle['cortinero_tergal_id'] ?? null;
        $dataDetalle['cortinero_tergal_cantidad'] = $detalle['cortinero_tergal_cantidad'] ?? null;
        $dataDetalle['cortinero_tergal_precio'] = $detalle['cortinero_tergal_precio'] ?? null;

        $detalleCotizacionCreado = $cotizacion->detalleCotizacion()->create($dataDetalle);

        // 🧩 Guardar insumos dinámicos
        $todosLosInsumos = [];

        foreach ($detalle as $key => $value) {
            if (preg_match('/^otros(\d+)_nombre$/', $key, $matches)) {
                $index = $matches[1];
                $insumoRaw = $value;

                // Convertir string tipo "cortinero_4" a int
                $insumoId = Str::startsWith($insumoRaw, 'cortinero_')
                    ? intval(Str::after($insumoRaw, 'cortinero_'))
                    : intval($insumoRaw);

                $cantidad = floatval($detalle["otros{$index}_cantidad"] ?? 0);
                $precio = floatval($detalle["otros{$index}_precio"] ?? 0);
                $subtotal = $cantidad * $precio;

                if ($insumoId && $cantidad > 0 && $precio > 0) {
                    if (isset($todosLosInsumos[$insumoId])) {
                        $todosLosInsumos[$insumoId]['cantidad'] += $cantidad;
                        $todosLosInsumos[$insumoId]['subtotal'] += $subtotal;
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
        $insumos = Insumo::whereIn('id_tipo_insumo', [2, 7])->get();

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

        // Obtener cortineros seleccionados
        $cortineroSeleccionado = null;
        $cortineroTergalSeleccionado = null;

        if ($detalleCotizacion) {
            // Buscar cortinero por el ID guardado en detalleCotizacion
            if ($detalleCotizacion->cortinero_id) {
                $cortineroSeleccionado = $cotizacion->insumos()
                    ->where('insumo_id', $detalleCotizacion->cortinero_id)
                    ->first();
            }

            // Buscar cortinero tergal por el ID guardado en detalleCotizacion
            if ($detalleCotizacion->cortinero_tergal_id) {
                $cortineroTergalSeleccionado = $cotizacion->insumos()
                    ->where('insumo_id', $detalleCotizacion->cortinero_tergal_id)
                    ->first();
            }
        }

        return view('admin.cotizaciones.edit', compact(
            'cotizacion',
            'detalleCotizacion',
            'insumos',
            'insumosFijos',
            'manoObra',
            'telas',
            'tergales',
            'forros',
            'cortineros',
            'cortineroSeleccionado',
            'cortineroTergalSeleccionado'
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
        $cotizacion->aplicar_iva = $request->has('aplicar_iva');
        $cotizacion->descuento = $request->input('descuento', 0);
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
            $dataDetalle['tipo_cortina'] = $detalle['tipo_cortina'] ?? null;
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

        // DATOS GENERALES
        $dataDetalle['costo_total_tela_tergal_forro'] = $detalle['costo_total_tela_tergal_forro'] ?? null;
        $dataDetalle['costo_total_mano_obra'] = $detalle['costo_total_mano_obra'] ?? null;
        $dataDetalle['decorador_porcentaje'] = $totales['decorador_porcentaje'] ?? 15;

        // AGREGAR DATOS DE CORTINEROS A $dataDetalle
        $dataDetalle['cortinero_id'] = $detalle['cortinero_id'] ?? null;
        $dataDetalle['cortinero_cantidad'] = $detalle['cortinero_cantidad'] ?? null;
        $dataDetalle['cortinero_precio'] = $detalle['cortinero_precio'] ?? null;

        $dataDetalle['cortinero_tergal_id'] = $detalle['cortinero_tergal_id'] ?? null;
        $dataDetalle['cortinero_tergal_cantidad'] = $detalle['cortinero_tergal_cantidad'] ?? null;
        $dataDetalle['cortinero_tergal_precio'] = $detalle['cortinero_tergal_precio'] ?? null;

        // Actualizar detalle de cotización
        $detalleCotizacion->update($dataDetalle);

        // ===== MANEJO DE INSUMOS =====
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

        // Insumos fijos
        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])
            ->where('id_tipo_insumo', 2)
            ->get()
            ->keyBy('nombre');

        foreach (['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'] as $nombre) {
            $insumo = $insumosFijos->get($nombre);
            if ($insumo) {
                $key = strtolower($nombre);
                $cantidad = $detalle["{$key}_cantidad"] ?? 0;

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

        // Cortinero cortina
        $cortineroId = $detalle['cortinero_id'] ?? null;
        $cortineroCantidad = $detalle['cortinero_cantidad'] ?? 0;
        $cortineroPrecio = $detalle['cortinero_precio'] ?? 0;
        if ($cortineroId && $cortineroCantidad > 0) {
            $todosLosInsumos[$cortineroId] = [
                'cantidad' => $cortineroCantidad,
                'precio_unitario' => $cortineroPrecio,
                'subtotal' => $cortineroCantidad * $cortineroPrecio,
            ];
        }

        // Cortinero tergal
        $cortineroTergalId = $detalle['cortinero_tergal_id'] ?? null;
        $cortineroTergalCantidad = $detalle['cortinero_tergal_cantidad'] ?? 0;
        $cortineroTergalPrecio = $detalle['cortinero_tergal_precio'] ?? 0;
        if ($cortineroTergalId && $cortineroTergalCantidad > 0) {
            $todosLosInsumos[$cortineroTergalId] = [
                'cantidad' => $cortineroTergalCantidad,
                'precio_unitario' => $cortineroTergalPrecio,
                'subtotal' => $cortineroTergalCantidad * $cortineroTergalPrecio,
            ];
        }

        // Otros insumos dinámicos
        foreach ($detalle as $k => $v) {
            if (preg_match('/^otros(\d+)_nombre$/', $k, $matches)) {
                $index = $matches[1];

                // Limpiar insumoId si viene con prefijo como "cortinero_4"
                if (preg_match('/^([a-zA-Z]+_)?(\d+)$/', $v, $matchInsumo)) {
                    $insumoId = (int) $matchInsumo[2];
                } else {
                    continue; // ignora si no es válido
                }

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

        // Sincronizar insumos
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

    public function pdfDecorador(Cotizacion $cotizacion)
    {
        $pdf = Pdf::loadView('admin.cotizaciones.pdfdecorador', compact('cotizacion'));
        return $pdf->stream('decorador_'.$cotizacion->id.'.pdf');
    }
}
