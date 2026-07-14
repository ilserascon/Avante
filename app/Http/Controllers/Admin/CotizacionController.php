<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\TipoInsumo;
use App\Models\TipoProducto;
use Barryvdh\DomPDF\Facade\Pdf; // Usa la facade de DomPDF
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::query()->with('cliente');

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
        $insumos = Insumo::with('tipoInsumo')->whereIn('id_tipo_insumo', [2, 7])->get();
        $tiposInsumoCotizacion = TipoInsumo::whereIn('id', [2, 7])->orderBy('nombre')->get();

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

        $cortineros = Producto::where('id_tipo_producto', 1)
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) {
                // Keep a precio_publico alias to avoid touching existing view bindings.
                $producto->precio_publico = $producto->precio;
                return $producto;
            });
        $productos = Producto::with('tipoProducto')->orderBy('nombre')->get();
        $tiposProductoCotizacion = TipoProducto::orderBy('nombre')->get();

        return view('admin.cotizaciones.create', compact(
            'insumos',
            'tiposInsumoCotizacion',
            'insumosFijos',
            'manoObra',
            'telas',
            'tergales',
            'forros',
            'cortineros',
            'productos',
            'tiposProductoCotizacion'
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
        $detalles = $request->input('detalles', []);
        $insumosGlobales = $request->input('insumos', []);
        $productosGlobales = $request->input('productos', []);

        $cotizacion = new Cotizacion();
        $cotizacion->cliente_id = $validated['cliente_id'];
        $cotizacion->fecha = $validated['fecha'];

        $setCotizacionColumn = function (string $column, $value) use ($cotizacion): void {
            if (Schema::hasColumn('cotizaciones', $column)) {
                $cotizacion->{$column} = $value;
            }
        };

        $detallesParaGuardar = [];
        if (!empty($detalles) && is_array($detalles)) {
            $detallesParaGuardar = $detalles;
        } elseif (!empty($detalle) && is_array($detalle)) {
            $detallesParaGuardar = [$detalle];
        }

        // Backward compatibility in case frontend still sends global tabs nested in detalles.
        if (empty($insumosGlobales) && is_array($detallesParaGuardar)) {
            foreach ($detallesParaGuardar as $bloque) {
                if (!empty($bloque['insumos']) && is_array($bloque['insumos'])) {
                    $insumosGlobales = array_merge($insumosGlobales, $bloque['insumos']);
                }
            }
        }

        if (empty($productosGlobales) && is_array($detallesParaGuardar)) {
            foreach ($detallesParaGuardar as $bloque) {
                if (!empty($bloque['productos']) && is_array($bloque['productos'])) {
                    $productosGlobales = array_merge($productosGlobales, $bloque['productos']);
                }
            }
        }

        $setCotizacionColumn('utilidad', $totales['utilidad'] ?? null);
        $setCotizacionColumn('costo_decorador', $totales['costo_decorador'] ?? null);
        $setCotizacionColumn('precio_publico', $totales['precio_publico'] ?? null);
        $setCotizacionColumn('aplicar_iva', $request->has('aplicar_iva'));
        $setCotizacionColumn('descuento', $request->input('descuento', 0));
        $setCotizacionColumn('estatus', $request->input('estatus', 'solicitada'));

        $cotizacion->save();

        foreach ($detallesParaGuardar as $detalleBloque) {
            $dataDetalle = [];
            $tiposBloque = array_filter($detalleBloque['tipo'] ?? [], function ($v) {
                return $v !== '__dummy_cortina__' && $v !== '__dummy_tergal__';
            });

            $dataDetalle['lleva_cortina'] = in_array('cortina', $tiposBloque) ? 1 : 0;
            $dataDetalle['lleva_tergal'] = in_array('tergal', $tiposBloque) ? 1 : 0;
            $dataDetalle['lleva_forro'] = !empty($detalleBloque['lleva_forro']) ? 1 : 0;
            $dataDetalle['area'] = $detalleBloque['area'] ?? null;

            if ($dataDetalle['lleva_cortina']) {
                $dataDetalle['tela_id'] = $detalleBloque['tela_id'] ?? null;
                $dataDetalle['ancho_tela'] = $detalleBloque['ancho_tela'] ?? null;
                $dataDetalle['ancho'] = $detalleBloque['ancho'] ?? null;
                $dataDetalle['largo'] = $detalleBloque['largo'] ?? null;
                $dataDetalle['no_lienzos'] = $detalleBloque['no_lienzos'] ?? null;
                $dataDetalle['no_lienzos_redondeado'] = $detalleBloque['no_lienzos_redondeado'] ?? null;
                $dataDetalle['bastilla'] = $detalleBloque['valor_bastilla'] ?? null;
                $dataDetalle['descripcion'] = $detalleBloque['descripcion'] ?? trim(implode(' | ', array_filter([
                    $detalleBloque['descripcion_tela'] ?? null,
                    $detalleBloque['descripcion_tergal'] ?? null,
                    $detalleBloque['descripcion_forro'] ?? null,
                ])));
                $dataDetalle['descripcion_tela'] = $detalleBloque['descripcion_tela'] ?? null;
                $dataDetalle['total_tela'] = $detalleBloque['total_tela'] ?? null;
                $dataDetalle['precio_m2_tela'] = $detalleBloque['precio_m2_tela'] ?? null;
                $dataDetalle['total_tela_final'] = $detalleBloque['total_tela_final'] ?? null;
                $dataDetalle['m2_1'] = $detalleBloque['m2_1'] ?? null;
                $dataDetalle['costo_mano_obra_1'] = $detalleBloque['costo_mano_obra_1'] ?? null;
                $dataDetalle['total_mano_obra_1'] = $detalleBloque['total_mano_obra_1'] ?? null;
            }

            if ($dataDetalle['lleva_tergal']) {
                $dataDetalle['tergal_id'] = $detalleBloque['tergal_id'] ?? null;
                $dataDetalle['ancho_tergal'] = $detalleBloque['ancho_tergal'] ?? null;
                $dataDetalle['ancho_tergal_real'] = $detalleBloque['ancho_tergal_real'] ?? null;
                $dataDetalle['largo_tergal'] = $detalleBloque['largo_tergal'] ?? null;
                $dataDetalle['no_lienzos_tergal'] = $detalleBloque['no_lienzos_tergal'] ?? null;
                $dataDetalle['no_lienzos_redondeado_tergal'] = $detalleBloque['no_lienzos_redondeado_tergal'] ?? null;
                $dataDetalle['bastilla_tergal'] = $detalleBloque['valor_bastilla_tergal'] ?? null;
                $dataDetalle['descripcion_tergal'] = $detalleBloque['descripcion_tergal'] ?? null;
                $dataDetalle['total_tergal'] = $detalleBloque['total_tergal'] ?? null;
                $dataDetalle['precio_m2_tergal'] = $detalleBloque['precio_m2_tergal'] ?? null;
                $dataDetalle['total_tergal_final'] = $detalleBloque['total_tergal_final'] ?? null;
                $dataDetalle['m2_2'] = $detalleBloque['m2_2'] ?? null;
                $dataDetalle['costo_mano_obra_2'] = $detalleBloque['costo_mano_obra_2'] ?? null;
                $dataDetalle['total_mano_obra_2'] = $detalleBloque['total_mano_obra_2'] ?? null;
            }

            if ($dataDetalle['lleva_forro']) {
                $dataDetalle['forro_id'] = $detalleBloque['forro_id'] ?? null;
                $dataDetalle['ancho_forro'] = $detalleBloque['ancho_forro'] ?? null;
                $dataDetalle['ancho_forro_real'] = $detalleBloque['ancho_forro_real'] ?? null;
                $dataDetalle['largo_forro'] = $detalleBloque['largo_forro'] ?? null;
                $dataDetalle['no_lienzos_forro'] = $detalleBloque['no_lienzos_forro'] ?? null;
                $dataDetalle['no_lienzos_redondeado_forro'] = $detalleBloque['no_lienzos_redondeado_forro'] ?? null;
                $dataDetalle['bastilla_forro'] = $detalleBloque['valor_bastilla_forro'] ?? null;
                $dataDetalle['descripcion_forro'] = $detalleBloque['descripcion_forro'] ?? null;
                $dataDetalle['total_forro'] = $detalleBloque['total_forro'] ?? null;
                $dataDetalle['precio_m2_forro'] = $detalleBloque['precio_m2_forro'] ?? null;
                $dataDetalle['total_final_forro'] = $detalleBloque['total_final_forro'] ?? null;
            }

            $dataDetalle['costo_total_tela_tergal_forro'] = $detalleBloque['costo_total_tela_tergal_forro'] ?? null;
            $dataDetalle['costo_total_mano_obra'] = $detalleBloque['costo_total_mano_obra'] ?? null;
            $dataDetalle['decorador_porcentaje'] = $detalleBloque['decorador_porcentaje'] ?? ($totales['decorador_porcentaje'] ?? 15);
            $dataDetalle['cortinero_id'] = $detalleBloque['cortinero_id'] ?? null;
            $dataDetalle['cortinero_cantidad'] = $detalleBloque['cortinero_cantidad'] ?? null;
            $dataDetalle['cortinero_precio'] = $detalleBloque['cortinero_precio'] ?? null;
            $dataDetalle['cortinero_tergal_id'] = $detalleBloque['cortinero_tergal_id'] ?? null;
            $dataDetalle['cortinero_tergal_cantidad'] = $detalleBloque['cortinero_tergal_cantidad'] ?? null;
            $dataDetalle['cortinero_tergal_precio'] = $detalleBloque['cortinero_tergal_precio'] ?? null;

            $totalLienzosDetalle =
                (float) ($detalleBloque['no_lienzos_redondeado'] ?? 0) +
                (float) ($detalleBloque['no_lienzos_redondeado_tergal'] ?? 0) +
                (float) ($detalleBloque['no_lienzos_redondeado_forro'] ?? 0);

            $dataDetalle['total_lienzos'] = $detalleBloque['total_lienzos'] ?? ($totalLienzosDetalle > 0 ? $totalLienzosDetalle : null);
            $dataDetalle['total_m2_tela'] = $detalleBloque['total_m2_tela'] ?? ($detalleBloque['total_tela'] ?? null);
            $dataDetalle['total_m2_tergal'] = $detalleBloque['total_m2_tergal'] ?? ($detalleBloque['total_tergal'] ?? null);
            $dataDetalle['total_m2_forro'] = $detalleBloque['total_m2_forro'] ?? ($detalleBloque['total_forro'] ?? null);

            $materialesDetalle =
                ((float) ($detalleBloque['cortinero_cantidad'] ?? 0) * (float) ($detalleBloque['cortinero_precio'] ?? 0)) +
                ((float) ($detalleBloque['cortinero_tergal_cantidad'] ?? 0) * (float) ($detalleBloque['cortinero_tergal_precio'] ?? 0));

            $costoCortinaDetalle =
                (float) ($detalleBloque['costo_total_tela_tergal_forro'] ?? 0) +
                (float) ($detalleBloque['costo_total_mano_obra'] ?? 0) +
                $materialesDetalle;

            $dataDetalle['costo_cortina'] = $detalleBloque['costo_cortina'] ?? ($costoCortinaDetalle > 0 ? $costoCortinaDetalle : null);

            $cotizacion->detallesCotizacion()->create($dataDetalle);
        }

        $todosLosInsumos = [];
        $todosLosProductos = [];

        foreach ($insumosGlobales as $insumoFila) {
            $insumoId = (int) ($insumoFila['id'] ?? 0);
            $cantidad = floatval($insumoFila['cantidad'] ?? 0);
            $precio = floatval($insumoFila['precio'] ?? 0);
            if ($insumoId && $cantidad > 0 && $precio > 0) {
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

        foreach ($productosGlobales as $productoFila) {
            $productoId = (int) ($productoFila['id'] ?? 0);
            $cantidad = floatval($productoFila['cantidad'] ?? 0);
            $precio = floatval($productoFila['precio'] ?? 0);
            if ($productoId && $cantidad > 0 && $precio > 0) {
                if (isset($todosLosProductos[$productoId])) {
                    $todosLosProductos[$productoId]['cantidad'] += $cantidad;
                    $todosLosProductos[$productoId]['subtotal'] += $cantidad * $precio;
                } else {
                    $todosLosProductos[$productoId] = [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $cantidad * $precio,
                    ];
                }
            }
        }

        if (!empty($todosLosInsumos)) {
            $cotizacion->insumos()->attach($todosLosInsumos);
        }

        if (!empty($todosLosProductos)) {
            $cotizacion->productos()->attach($todosLosProductos);
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

        $setCotizacionColumn = function (string $column, $value) use ($cotizacion): void {
            if (Schema::hasColumn('cotizaciones', $column)) {
                $cotizacion->{$column} = $value;
            }
        };

        $tipos = $request->input('tipo', []);
        $llevaCortina = in_array('cortina', $tipos);
        $llevaTergal = in_array('tergal', $tipos);
        $llevaForro = $request->has('lleva_forro');
        $cotizacion->cliente_id   = $validated['cliente_id'];
        $cotizacion->fecha        = $validated['fecha'];
        $setCotizacionColumn('utilidad', $totales['utilidad'] ?? null);
        $setCotizacionColumn('costo_decorador', $totales['costo_decorador'] ?? null);
        $setCotizacionColumn('precio_publico', $totales['precio_publico'] ?? null);
        $setCotizacionColumn('aplicar_iva', $request->has('aplicar_iva'));
        $setCotizacionColumn('descuento', $request->input('descuento', 0));
        $setCotizacionColumn('estatus', $request->input('estatus', 'solicitada'));
        $cotizacion->save();

        $detalleCotizacion = $cotizacion->detalleCotizacion;
        if (!$detalleCotizacion) {
            $detalleCotizacion = $cotizacion->detalleCotizacion()->create([]);
        }

        $dataDetalle = [];

        // CORTINA
        $dataDetalle['lleva_cortina'] = $llevaCortina ? 1 : 0;
        $dataDetalle['lleva_tergal'] = $llevaTergal ? 1 : 0;
        $dataDetalle['lleva_forro'] = $llevaForro ? 1 : 0;

        if ($llevaCortina) {
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
            $dataDetalle['descripcion'] = $detalle['descripcion'] ?? trim(implode(' | ', array_filter([
                $detalle['descripcion_tela'] ?? null,
                $detalle['descripcion_tergal'] ?? null,
                $detalle['descripcion_forro'] ?? null,
            ])));
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
        if ($llevaTergal) {
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
        if ($llevaForro) {
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
        $dataDetalle['area'] = $detalle['area'] ?? null;
        $dataDetalle['total_lienzos'] = $totales['total_lienzos'] ?? null;
        $dataDetalle['total_m2_forro'] = $totales['total_m2_forro'] ?? null;
        $dataDetalle['total_m2_tela'] = $totales['total_m2_tela'] ?? null;
        $dataDetalle['total_m2_tergal'] = $totales['total_m2_tergal'] ?? null;
        $dataDetalle['costo_cortina'] = $totales['costo_cortina'] ?? null;
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

    private function guardarDetalleCotizacion(Cotizacion $cotizacion, array $detalleData, array $totales = []): void
    {
        $tipoSeleccionado = (array) ($detalleData['tipo'] ?? []);

        $dataDetalle = [
            'lleva_cortina' => in_array('cortina', $tipoSeleccionado) ? 1 : 0,
            'lleva_tergal' => in_array('tergal', $tipoSeleccionado) ? 1 : 0,
            'lleva_forro' => !empty($detalleData['lleva_forro']) ? 1 : 0,
            'tela_id' => $detalleData['tela_id'] ?? null,
            'ancho_tela' => $detalleData['ancho_tela'] ?? null,
            'ancho' => $detalleData['ancho'] ?? null,
            'largo' => $detalleData['largo'] ?? null,
            'no_lienzos' => $detalleData['no_lienzos'] ?? null,
            'no_lienzos_redondeado' => $detalleData['no_lienzos_redondeado'] ?? null,
            'bastilla' => $detalleData['valor_bastilla'] ?? null,
            'descripcion' => $detalleData['descripcion'] ?? trim(implode(' | ', array_filter([
                $detalleData['descripcion_tela'] ?? null,
                $detalleData['descripcion_tergal'] ?? null,
                $detalleData['descripcion_forro'] ?? null,
            ]))),
            'tergal_id' => $detalleData['tergal_id'] ?? null,
            'ancho_tergal' => $detalleData['ancho_tergal'] ?? null,
            'ancho_tergal_real' => $detalleData['ancho_tergal_real'] ?? null,
            'largo_tergal' => $detalleData['largo_tergal'] ?? null,
            'no_lienzos_tergal' => $detalleData['no_lienzos_tergal'] ?? null,
            'no_lienzos_redondeado_tergal' => $detalleData['no_lienzos_redondeado_tergal'] ?? null,
            'bastilla_tergal' => $detalleData['valor_bastilla_tergal'] ?? null,
            'forro_id' => $detalleData['forro_id'] ?? null,
            'ancho_forro' => $detalleData['ancho_forro'] ?? null,
            'ancho_forro_real' => $detalleData['ancho_forro_real'] ?? null,
            'largo_forro' => $detalleData['largo_forro'] ?? null,
            'no_lienzos_forro' => $detalleData['no_lienzos_forro'] ?? null,
            'no_lienzos_redondeado_forro' => $detalleData['no_lienzos_redondeado_forro'] ?? null,
            'bastilla_forro' => $detalleData['valor_bastilla_forro'] ?? null,
            'total_tela' => $detalleData['total_tela'] ?? null,
            'precio_m2_tela' => $detalleData['precio_m2_tela'] ?? null,
            'descripcion_tela' => $detalleData['descripcion_tela'] ?? null,
            'total_tela_final' => $detalleData['total_tela_final'] ?? null,
            'total_tergal' => $detalleData['total_tergal'] ?? null,
            'precio_m2_tergal' => $detalleData['precio_m2_tergal'] ?? null,
            'descripcion_tergal' => $detalleData['descripcion_tergal'] ?? null,
            'total_tergal_final' => $detalleData['total_tergal_final'] ?? null,
            'total_forro' => $detalleData['total_forro'] ?? null,
            'precio_m2_forro' => $detalleData['precio_m2_forro'] ?? null,
            'descripcion_forro' => $detalleData['descripcion_forro'] ?? null,
            'total_final_forro' => $detalleData['total_final_forro'] ?? null,
            'costo_total_tela_tergal_forro' => $detalleData['costo_total_tela_tergal_forro'] ?? null,
            'm2_1' => $detalleData['m2_1'] ?? null,
            'costo_mano_obra_1' => $detalleData['costo_mano_obra_1'] ?? null,
            'total_mano_obra_1' => $detalleData['total_mano_obra_1'] ?? null,
            'm2_2' => $detalleData['m2_2'] ?? null,
            'costo_mano_obra_2' => $detalleData['costo_mano_obra_2'] ?? null,
            'total_mano_obra_2' => $detalleData['total_mano_obra_2'] ?? null,
            'costo_total_mano_obra' => $detalleData['costo_total_mano_obra'] ?? null,
            'decorador_porcentaje' => $totales['decorador_porcentaje'] ?? null,
            'cortinero_id' => $detalleData['cortinero_id'] ?? null,
            'cortinero_cantidad' => $detalleData['cortinero_cantidad'] ?? null,
            'cortinero_precio' => $detalleData['cortinero_precio'] ?? null,
            'cortinero_tergal_id' => $detalleData['cortinero_tergal_id'] ?? null,
            'cortinero_tergal_cantidad' => $detalleData['cortinero_tergal_cantidad'] ?? null,
            'cortinero_tergal_precio' => $detalleData['cortinero_tergal_precio'] ?? null,
        ];

        $detalleCotizacion = $cotizacion->detallesCotizacion()->create($dataDetalle);

        $insumosParaSincronizar = [];
        foreach ($detalleData['insumos'] ?? [] as $insumoRow) {
            $insumoId = $insumoRow['id'] ?? null;
            $cantidad = (float) ($insumoRow['cantidad'] ?? 0);
            $precio = (float) ($insumoRow['precio'] ?? 0);
            if ($insumoId && $cantidad > 0) {
                $insumosParaSincronizar[$insumoId] = [
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $cantidad * $precio,
                ];
            }
        }

        foreach ($detalleData as $key => $value) {
            if (preg_match('/^otros(\d+)_nombre$/', $key, $matches)) {
                $index = $matches[1];
                $insumoRaw = $value;
                $insumoId = Str::startsWith($insumoRaw, 'cortinero_')
                    ? intval(Str::after($insumoRaw, 'cortinero_'))
                    : intval($insumoRaw);
                $cantidad = (float) ($detalleData["otros{$index}_cantidad"] ?? 0);
                $precio = (float) ($detalleData["otros{$index}_precio"] ?? 0);
                if ($insumoId && $cantidad > 0) {
                    $insumosParaSincronizar[$insumoId] = [
                        'cantidad' => ($insumosParaSincronizar[$insumoId]['cantidad'] ?? 0) + $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => (($insumosParaSincronizar[$insumoId]['subtotal'] ?? 0) + ($cantidad * $precio)),
                    ];
                }
            }
        }

        if (!empty($insumosParaSincronizar)) {
            $cotizacion->insumos()->syncWithoutDetaching($insumosParaSincronizar);
        }

        $productosParaSincronizar = [];
        foreach ($detalleData['productos'] ?? [] as $productoRow) {
            $productoId = $productoRow['id'] ?? null;
            $cantidad = (float) ($productoRow['cantidad'] ?? 0);
            $precio = (float) ($productoRow['precio'] ?? 0);
            if ($productoId && $cantidad > 0) {
                $productosParaSincronizar[$productoId] = [
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $cantidad * $precio,
                ];
            }
        }

        if (!empty($productosParaSincronizar)) {
            $cotizacion->productos()->syncWithoutDetaching($productosParaSincronizar);
        }
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
