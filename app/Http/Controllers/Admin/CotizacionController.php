<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CotizacionInventarioService;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\TipoInsumo;
use App\Models\TipoProducto;
use Barryvdh\DomPDF\Facade\Pdf; // Usa la facade de DomPDF
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;

class CotizacionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->puedeEditarCotizacion()) {
                abort(403, 'No tienes permiso para crear o editar cotizaciones.');
            }

            return $next($request);
        })->only(['create', 'store', 'edit', 'update']);

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->puedeGestionarEstatusCotizacion()) {
                abort(403, 'No tienes permiso para cambiar el estatus de cotizaciones.');
            }

            return $next($request);
        })->only(['cambiarEstatus']);
    }

    public function index(Request $request)
    {
        $query = Cotizacion::query()->with(['cliente', 'creadoPor']);

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        if ($request->filled('folio')) {
            $digitos = preg_replace('/\D/', '', trim($request->input('folio')));
            if ($digitos !== '') {
                $query->whereRaw("LPAD(cotizaciones.id, 5, '0') LIKE ?", ['%' . $digitos . '%']);
            }
        }

        if ($request->filled('cliente')) {
            $cliente = trim($request->input('cliente'));
            $query->whereHas('cliente', function ($q) use ($cliente) {
                $q->where('nombre', 'like', '%' . $cliente . '%');
            });
        }

        $cotizaciones = $query->latest()->paginate(10)->withQueryString();

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $insumos = $this->insumosParaTabCotizacion();
        $tiposInsumoCotizacion = $this->tiposInsumoParaCotizacion();

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
            ->get();
        $productos = Producto::with('tipoProducto')->orderBy('nombre')->get();
        $productosDisponibles = $this->mapProductosParaCotizacion();
        $tiposProductoCotizacion = TipoProducto::orderBy('nombre')->get();

        $insumosMaterialesVarios = $this->insumosParaMaterialesVarios();

        return view('admin.cotizaciones.create', compact(
            'insumos',
            'insumosMaterialesVarios',
            'tiposInsumoCotizacion',
            'insumosFijos',
            'manoObra',
            'telas',
            'tergales',
            'forros',
            'cortineros',
            'productos',
            'productosDisponibles',
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
        $cotizacion->user_id = auth()->id();

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

        $detallesParaGuardar = $this->filtrarDetallesConContenido($detallesParaGuardar);

        if (empty($insumosGlobales) && is_array($detalles)) {
            foreach ($detalles as $bloque) {
                if (!empty($bloque['insumos']) && is_array($bloque['insumos'])) {
                    $insumosGlobales = array_merge($insumosGlobales, $bloque['insumos']);
                }
            }
        }

        if (empty($productosGlobales) && is_array($detalles)) {
            foreach ($detalles as $bloque) {
                if (!empty($bloque['productos']) && is_array($bloque['productos'])) {
                    $productosGlobales = array_merge($productosGlobales, $bloque['productos']);
                }
            }
        }

        if (empty($detallesParaGuardar) && !$this->tieneInsumosOProductos($insumosGlobales, $productosGlobales)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['detalles' => 'Debe agregar al menos un concepto (Cortina, Tergal o Forro) o registrar insumos/productos.']);
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
        $setCotizacionColumn('precio_publico', $this->calcularPrecioPublicoCotizacion(
            $request,
            $detallesParaGuardar,
            $insumosGlobales,
            $productosGlobales,
            $totales['precio_publico'] ?? null
        ));
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
            $dataDetalle['descuento'] = $detalleBloque['descuento'] ?? ($request->input('descuento', 0));
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
                ((float) ($detalleBloque['cortinero_tergal_cantidad'] ?? 0) * (float) ($detalleBloque['cortinero_tergal_precio'] ?? 0)) +
                $this->sumarMaterialesOtros($detalleBloque);

            $costoCortinaDetalle =
                (float) ($detalleBloque['costo_total_tela_tergal_forro'] ?? 0) +
                (float) ($detalleBloque['costo_total_mano_obra'] ?? 0) +
                $materialesDetalle;

            $dataDetalle['costo_cortina'] = $detalleBloque['costo_cortina'] ?? ($costoCortinaDetalle > 0 ? $costoCortinaDetalle : null);
            $dataDetalle['materiales_varios'] = $this->materialesVariosDesdeDetalleBloque($detalleBloque);

            $cotizacion->detallesCotizacion()->create($dataDetalle);
        }

        $todosLosInsumos = [];
        $todosLosProductos = [];

        foreach ($insumosGlobales as $insumoFila) {
            $this->acumularFilaPivot(
                $todosLosInsumos,
                (int) ($insumoFila['id'] ?? 0),
                (float) ($insumoFila['cantidad'] ?? 0),
                (float) ($insumoFila['precio'] ?? 0),
                (float) ($insumoFila['descuento'] ?? 0)
            );
        }

        foreach ($productosGlobales as $productoFila) {
            $this->acumularFilaPivot(
                $todosLosProductos,
                (int) ($productoFila['id'] ?? 0),
                (float) ($productoFila['cantidad'] ?? 0),
                (float) ($productoFila['precio'] ?? 0),
                (float) ($productoFila['descuento'] ?? 0)
            );
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
        $cotizacion = Cotizacion::with([
            'cliente',
            'creadoPor',
            'insumos.tipoInsumo',
            'productos.tipoProducto',
            'detallesCotizacion.tela',
            'detallesCotizacion.tergal',
            'detallesCotizacion.forro',
            'detallesCotizacion.cortinero',
            'detallesCotizacion.cortineroTergal',
            'detalleCotizacion.tela',
            'detalleCotizacion.tergal',
            'detalleCotizacion.forro',
            'detalleCotizacion.cortinero',
            'detalleCotizacion.cortineroTergal',
        ])->findOrFail($id);

        $detalles = $cotizacion->detallesCotizacion;
        if ($detalles->isEmpty() && $cotizacion->detalleCotizacion) {
            $detalles = collect([$cotizacion->detalleCotizacion]);
        }

        return view('admin.cotizaciones.show', compact('cotizacion', 'detalles'));
    }

    public function edit($id)
    {
        $cotizacion = Cotizacion::with([
            'insumos.tipoInsumo',
            'productos.tipoProducto',
            'detallesCotizacion',
            'detalleCotizacion',
        ])->findOrFail($id);

        if ($redirect = $this->redireccionSiCotizacionNoEditable($cotizacion)) {
            return $redirect;
        }

        $detalleCotizacion = $cotizacion->detalleCotizacion;

        $insumos = $this->insumosParaTabCotizacion();
        $tiposInsumoCotizacion = $this->tiposInsumoParaCotizacion();

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

        $cortineros = Producto::where('id_tipo_producto', 1)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::with('tipoProducto')->orderBy('nombre')->get();
        $productosDisponibles = $this->mapProductosParaCotizacion();
        $tiposProductoCotizacion = TipoProducto::orderBy('nombre')->get();

        $detallesExistentes = $cotizacion->detallesCotizacion;
        if ($detallesExistentes->isEmpty() && $detalleCotizacion) {
            $detallesExistentes = collect([$detalleCotizacion]);
        }

        $cortineroIds = $detallesExistentes
            ->flatMap(function ($detalle) {
                return array_filter([
                    $detalle->cortinero_id,
                    $detalle->cortinero_tergal_id,
                ]);
            })
            ->unique()
            ->values()
            ->all();

        $descuentoCotizacion = $cotizacion->descuento ?? 0;
        $detallesExistentes = $detallesExistentes->map(function ($detalle) use ($descuentoCotizacion) {
            $data = $detalle->toArray();
            if (empty($data['descuento']) && $descuentoCotizacion) {
                $data['descuento'] = $descuentoCotizacion;
            }
            return $data;
        })->values();

        $insumosExistentes = $cotizacion->insumos
            ->reject(function ($insumo) use ($cortineroIds) {
                return in_array($insumo->id, $cortineroIds, true);
            })
            ->map(function ($insumo) {
                return [
                    'tipo_id' => $insumo->id_tipo_insumo,
                    'id' => $insumo->id,
                    'cantidad' => $insumo->pivot->cantidad,
                    'precio' => $insumo->pivot->precio_unitario,
                    'descuento' => $insumo->pivot->descuento ?? 0,
                ];
            })
            ->values();

        $productosExistentes = $cotizacion->productos
            ->map(function ($producto) {
                return [
                    'tipo_id' => $producto->id_tipo_producto,
                    'id' => $producto->id,
                    'cantidad' => $producto->pivot->cantidad,
                    'precio' => $producto->pivot->precio_unitario,
                    'descuento' => $producto->pivot->descuento ?? 0,
                ];
            })
            ->values();

        $insumosMaterialesVarios = $this->insumosParaMaterialesVarios();

        return view('admin.cotizaciones.edit', compact(
            'cotizacion',
            'detalleCotizacion',
            'detallesExistentes',
            'insumosExistentes',
            'productosExistentes',
            'insumos',
            'insumosMaterialesVarios',
            'tiposInsumoCotizacion',
            'insumosFijos',
            'manoObra',
            'telas',
            'tergales',
            'forros',
            'cortineros',
            'productos',
            'productosDisponibles',
            'tiposProductoCotizacion'
        ));
    }

    public function update(Request $request, $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        if ($redirect = $this->redireccionSiCotizacionNoEditable($cotizacion)) {
            return $redirect;
        }

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

        $detallesParaGuardar = $this->filtrarDetallesConContenido($detallesParaGuardar);

        if (empty($insumosGlobales) && is_array($detalles)) {
            foreach ($detalles as $bloque) {
                if (!empty($bloque['insumos']) && is_array($bloque['insumos'])) {
                    $insumosGlobales = array_merge($insumosGlobales, $bloque['insumos']);
                }
            }
        }

        if (empty($productosGlobales) && is_array($detalles)) {
            foreach ($detalles as $bloque) {
                if (!empty($bloque['productos']) && is_array($bloque['productos'])) {
                    $productosGlobales = array_merge($productosGlobales, $bloque['productos']);
                }
            }
        }

        if (empty($detallesParaGuardar) && !$this->tieneInsumosOProductos($insumosGlobales, $productosGlobales)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['detalles' => 'Debe agregar al menos un concepto (Cortina, Tergal o Forro) o registrar insumos/productos.']);
        }

        try {
            DB::transaction(function () use (
                $cotizacion,
                $validated,
                $setCotizacionColumn,
                $totales,
                $request,
                $detallesParaGuardar,
                $insumosGlobales,
                $productosGlobales
            ) {
        $cotizacion->cliente_id = $validated['cliente_id'];
        $cotizacion->fecha = $validated['fecha'];
        $setCotizacionColumn('utilidad', $totales['utilidad'] ?? null);
        $setCotizacionColumn('costo_decorador', $totales['costo_decorador'] ?? null);
        $setCotizacionColumn('precio_publico', $this->calcularPrecioPublicoCotizacion(
            $request,
            $detallesParaGuardar,
            $insumosGlobales,
            $productosGlobales,
            $totales['precio_publico'] ?? null
        ));
        $setCotizacionColumn('aplicar_iva', $request->has('aplicar_iva'));
        $setCotizacionColumn('descuento', $request->input('descuento', 0));
        $cotizacion->save();

        $cotizacion->detallesCotizacion()->delete();

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
            $dataDetalle['descuento'] = $detalleBloque['descuento'] ?? ($request->input('descuento', 0));
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
                ((float) ($detalleBloque['cortinero_tergal_cantidad'] ?? 0) * (float) ($detalleBloque['cortinero_tergal_precio'] ?? 0)) +
                $this->sumarMaterialesOtros($detalleBloque);

            $costoCortinaDetalle =
                (float) ($detalleBloque['costo_total_tela_tergal_forro'] ?? 0) +
                (float) ($detalleBloque['costo_total_mano_obra'] ?? 0) +
                $materialesDetalle;

            $dataDetalle['costo_cortina'] = $detalleBloque['costo_cortina'] ?? ($costoCortinaDetalle > 0 ? $costoCortinaDetalle : null);
            $dataDetalle['materiales_varios'] = $this->materialesVariosDesdeDetalleBloque($detalleBloque);

            $cotizacion->detallesCotizacion()->create($dataDetalle);
        }

        $todosLosInsumos = [];
        $todosLosProductos = [];

        foreach ($insumosGlobales as $insumoFila) {
            $this->acumularFilaPivot(
                $todosLosInsumos,
                (int) ($insumoFila['id'] ?? 0),
                (float) ($insumoFila['cantidad'] ?? 0),
                (float) ($insumoFila['precio'] ?? 0),
                (float) ($insumoFila['descuento'] ?? 0)
            );
        }

        foreach ($productosGlobales as $productoFila) {
            $this->acumularFilaPivot(
                $todosLosProductos,
                (int) ($productoFila['id'] ?? 0),
                (float) ($productoFila['cantidad'] ?? 0),
                (float) ($productoFila['precio'] ?? 0),
                (float) ($productoFila['descuento'] ?? 0)
            );
        }

        $cotizacion->insumos()->sync($todosLosInsumos);
        $cotizacion->productos()->sync($todosLosProductos);
            });
        } catch (RuntimeException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.cotizaciones.index')
            ->with('success', 'Cotización actualizada exitosamente.');
    }

    private function detalleTieneContenido(array $detalleBloque): bool
    {
        $tiposBloque = array_filter($detalleBloque['tipo'] ?? [], function ($v) {
            return $v !== '__dummy_cortina__' && $v !== '__dummy_tergal__';
        });

        return in_array('cortina', $tiposBloque, true)
            || in_array('tergal', $tiposBloque, true)
            || !empty($detalleBloque['lleva_forro']);
    }

    private function filtrarDetallesConContenido(array $detalles): array
    {
        return array_values(array_filter($detalles, function ($detalleBloque) {
            return is_array($detalleBloque) && $this->detalleTieneContenido($detalleBloque);
        }));
    }

    private function tieneInsumosOProductos(array $insumos, array $productos): bool
    {
        foreach ($insumos as $fila) {
            $insumoId = (int) ($fila['id'] ?? 0);
            $cantidad = (float) ($fila['cantidad'] ?? 0);
            $precio = (float) ($fila['precio'] ?? 0);
            if ($insumoId && $cantidad > 0 && $precio > 0) {
                return true;
            }
        }

        foreach ($productos as $fila) {
            $productoId = (int) ($fila['id'] ?? 0);
            $cantidad = (float) ($fila['cantidad'] ?? 0);
            $precio = (float) ($fila['precio'] ?? 0);
            if ($productoId && $cantidad > 0 && $precio > 0) {
                return true;
            }
        }

        return false;
    }

    private function calcularSubtotalLinea(float $cantidad, float $precio, float $descuento = 0): float
    {
        $bruto = $cantidad * $precio;
        if ($descuento > 0) {
            return round($bruto - ($bruto * ($descuento / 100)), 2);
        }

        return round($bruto, 2);
    }

    private function acumularFilaPivot(array &$acumulado, int $id, float $cantidad, float $precio, float $descuento = 0): void
    {
        if ($id <= 0 || $cantidad <= 0 || $precio <= 0) {
            return;
        }

        $subtotalLinea = $this->calcularSubtotalLinea($cantidad, $precio, $descuento);

        if (isset($acumulado[$id])) {
            $cantidadTotal = (float) $acumulado[$id]['cantidad'] + $cantidad;
            $precioUnitario = (float) $acumulado[$id]['precio_unitario'];
            $subtotalTotal = (float) $acumulado[$id]['subtotal'] + $subtotalLinea;
            $brutoTotal = $cantidadTotal * $precioUnitario;

            $acumulado[$id] = [
                'cantidad' => $cantidadTotal,
                'precio_unitario' => $precioUnitario,
                'descuento' => $brutoTotal > 0 ? round((1 - ($subtotalTotal / $brutoTotal)) * 100, 2) : 0,
                'subtotal' => round($subtotalTotal, 2),
            ];

            return;
        }

        $acumulado[$id] = [
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'descuento' => $descuento > 0 ? round($descuento, 2) : 0,
            'subtotal' => $subtotalLinea,
        ];
    }

    private function calcularPrecioPublicoCotizacion(
        Request $request,
        array $detallesParaGuardar,
        array $insumosGlobales,
        array $productosGlobales,
        $precioPublicoForm = null
    ): ?float {
        $total = 0.0;
        $descuentoGlobal = (float) $request->input('descuento', 0);

        foreach ($detallesParaGuardar as $detalleBloque) {
            $materiales =
                ((float) ($detalleBloque['cortinero_cantidad'] ?? 0) * (float) ($detalleBloque['cortinero_precio'] ?? 0)) +
                ((float) ($detalleBloque['cortinero_tergal_cantidad'] ?? 0) * (float) ($detalleBloque['cortinero_tergal_precio'] ?? 0)) +
                $this->sumarMaterialesOtros($detalleBloque);

            $costoCortina = (float) ($detalleBloque['costo_cortina'] ?? 0);
            if ($costoCortina <= 0) {
                $costoCortina =
                    (float) ($detalleBloque['costo_total_tela_tergal_forro'] ?? 0) +
                    (float) ($detalleBloque['costo_total_mano_obra'] ?? 0) +
                    $materiales;
            }

            $precioDetalle = $costoCortina * 2;
            $descuentoDetalle = (float) ($detalleBloque['descuento'] ?? $descuentoGlobal);
            if ($descuentoDetalle > 0) {
                $precioDetalle -= $precioDetalle * ($descuentoDetalle / 100);
            }

            $total += $precioDetalle;
        }

        foreach ($insumosGlobales as $insumoFila) {
            $cantidad = (float) ($insumoFila['cantidad'] ?? 0);
            $precio = (float) ($insumoFila['precio'] ?? 0);
            $descuento = (float) ($insumoFila['descuento'] ?? 0);
            if ((int) ($insumoFila['id'] ?? 0) && $cantidad > 0 && $precio > 0) {
                $total += $this->calcularSubtotalLinea($cantidad, $precio, $descuento);
            }
        }

        foreach ($productosGlobales as $productoFila) {
            $cantidad = (float) ($productoFila['cantidad'] ?? 0);
            $precio = (float) ($productoFila['precio'] ?? 0);
            $descuento = (float) ($productoFila['descuento'] ?? 0);
            if ((int) ($productoFila['id'] ?? 0) && $cantidad > 0 && $precio > 0) {
                $total += $this->calcularSubtotalLinea($cantidad, $precio, $descuento);
            }
        }

        if ($request->has('aplicar_iva')) {
            $total *= 1.16;
        }

        if ($total > 0) {
            return round($total, 2);
        }

        $precioForm = (float) ($precioPublicoForm ?? 0);

        return $precioForm > 0 ? round($precioForm, 2) : null;
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
            'descuento' => $detalleData['descuento'] ?? null,
            'cortinero_id' => $detalleData['cortinero_id'] ?? null,
            'cortinero_cantidad' => $detalleData['cortinero_cantidad'] ?? null,
            'cortinero_precio' => $detalleData['cortinero_precio'] ?? null,
            'cortinero_tergal_id' => $detalleData['cortinero_tergal_id'] ?? null,
            'cortinero_tergal_cantidad' => $detalleData['cortinero_tergal_cantidad'] ?? null,
            'cortinero_tergal_precio' => $detalleData['cortinero_tergal_precio'] ?? null,
            'materiales_varios' => $this->materialesVariosDesdeDetalleBloque($detalleData),
        ];

        $cotizacion->detallesCotizacion()->create($dataDetalle);

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
        $request->validate([
            'estatus' => 'required|in:solicitada,aceptada,rechazada,completada,cancelada',
        ]);

        $estatusAnterior = $cotizacion->estatus;
        $estatusNuevo = $request->estatus;

        if ($estatusNuevo === $estatusAnterior) {
            return redirect()->back()->with('success', 'Estatus actualizado correctamente.');
        }

        if ($estatusNuevo === 'completada' && $estatusAnterior !== 'aceptada') {
            return redirect()->back()->with('error', 'Solo se puede completar una cotización que esté aceptada.');
        }

        if ($estatusNuevo === 'cancelada' && $estatusAnterior !== 'aceptada') {
            return redirect()->back()->with('error', 'Solo se puede cancelar una cotización que esté aceptada.');
        }

        $inventarioService = app(CotizacionInventarioService::class);
        $advertenciaFaltantes = null;

        try {
            DB::transaction(function () use ($cotizacion, $estatusAnterior, $estatusNuevo, $inventarioService, &$advertenciaFaltantes) {
                if ($estatusNuevo === 'completada' && $estatusAnterior !== 'completada') {
                    $errorInventario = $inventarioService->procesarCompletado($cotizacion);
                    if ($errorInventario !== null) {
                        throw new RuntimeException($errorInventario);
                    }
                }

                $cotizacion->estatus = $estatusNuevo;
                $cotizacion->save();

                if ($estatusNuevo === 'aceptada' && $estatusAnterior !== 'aceptada') {
                    $faltantes = $inventarioService->listarFaltantes($cotizacion);
                    if (!empty($faltantes)) {
                        $advertenciaFaltantes = $inventarioService->mensajeAceptadaConFaltantes($faltantes);
                    }
                }
            });
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $redirect = redirect()->back()->with('success', $this->mensajeExitoEstatus($estatusNuevo));

        if ($advertenciaFaltantes !== null) {
            $redirect->with('warning', $advertenciaFaltantes);
        }

        return $redirect;
    }

    private function redireccionSiCotizacionNoEditable(Cotizacion $cotizacion)
    {
        if (!in_array($cotizacion->estatus, ['aceptada', 'completada'], true)) {
            return null;
        }

        return redirect()
            ->route('admin.cotizaciones.show', $cotizacion->id)
            ->with('error', 'No se puede editar una cotización aceptada o completada.');
    }

    private function tiposInsumoParaCotizacion()
    {
        return TipoInsumo::orderBy('nombre')->get();
    }

    private function insumosParaTabCotizacion()
    {
        return Insumo::with('tipoInsumo')
            ->where('borrado', 0)
            ->orderBy('nombre')
            ->get()
            ->each(function ($insumo) {
                $insumo->etiqueta = $insumo->etiquetaCotizacion();
            });
    }

    /** @var list<string> */
    private const TIPOS_INSUMO_EXCLUIDOS_MATERIALES_VARIOS = ['Mano de Obra', 'Telas', 'Tergal', 'Forro'];

    private function insumosParaMaterialesVarios()
    {
        $tipoIds = TipoInsumo::whereNotIn('nombre', self::TIPOS_INSUMO_EXCLUIDOS_MATERIALES_VARIOS)->pluck('id');

        return Insumo::with('tipoInsumo')
            ->whereIn('id_tipo_insumo', $tipoIds)
            ->where('borrado', 0)
            ->orderBy('nombre')
            ->get()
            ->map(function ($insumo) {
                return [
                    'id' => $insumo->id,
                    'nombre' => $insumo->nombre,
                    'clave' => $insumo->clave,
                    'color' => $insumo->color,
                    'etiqueta' => $insumo->etiquetaCotizacion(),
                    'costo' => $insumo->costo,
                    'id_tipo_insumo' => $insumo->id_tipo_insumo,
                ];
            })
            ->values();
    }

    private function sumarMaterialesOtros(array $detalleBloque): float
    {
        $sum = 0.0;
        foreach ($this->extraerOtrosInsumosDesdeDetalle($detalleBloque) as $fila) {
            $sum += $fila['cantidad'] * $fila['precio'];
        }

        return $sum;
    }

    /**
     * @return list<array{id: int, cantidad: float, precio: float}>
     */
    private function extraerOtrosInsumosDesdeDetalle(array $detalleBloque): array
    {
        $filas = [];
        foreach ($detalleBloque as $key => $value) {
            if (! preg_match('/^otros(\d+)_nombre$/', (string) $key, $matches)) {
                continue;
            }
            $idx = $matches[1];
            $insumoRaw = $value;
            if ($insumoRaw === null || $insumoRaw === '') {
                continue;
            }
            $insumoId = is_string($insumoRaw) && Str::startsWith($insumoRaw, 'cortinero_')
                ? (int) Str::after($insumoRaw, 'cortinero_')
                : (int) $insumoRaw;
            $cantidad = (float) ($detalleBloque["otros{$idx}_cantidad"] ?? 0);
            $precio = (float) ($detalleBloque["otros{$idx}_precio"] ?? 0);
            if ($insumoId > 0 && $cantidad > 0 && $precio > 0) {
                $filas[] = [
                    'id' => $insumoId,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                ];
            }
        }

        return $filas;
    }

    /**
     * @return list<array{insumo_id: int, cantidad: float, precio_unitario: float, subtotal: float}>|null
     */
    private function materialesVariosDesdeDetalleBloque(array $detalleBloque): ?array
    {
        $filas = $this->extraerOtrosInsumosDesdeDetalle($detalleBloque);
        if ($filas === []) {
            return null;
        }

        return array_values(array_map(function (array $fila) {
            $subtotal = round($fila['cantidad'] * $fila['precio'], 2);

            return [
                'insumo_id' => (int) $fila['id'],
                'cantidad' => (float) $fila['cantidad'],
                'precio_unitario' => (float) $fila['precio'],
                'subtotal' => $subtotal,
            ];
        }, $filas));
    }

    private function mensajeExitoEstatus(string $estatus): string
    {
        return match ($estatus) {
            'aceptada' => 'Cotización aceptada correctamente.',
            'completada' => 'Cotización completada e inventario descontado correctamente.',
            'rechazada' => 'Cotización rechazada correctamente.',
            'cancelada' => 'Cotización cancelada correctamente.',
            default => 'Estatus actualizado correctamente.',
        };
    }

    public function generarPdf($id)
    {
        [$cotizacion, $detalles, $output, $fileName] = $this->buildPdfCliente((int) $id);

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function compartirPdf(Cotizacion $cotizacion, string $token)
    {
        if (! hash_equals($cotizacion->shareToken(), $token)) {
            abort(404);
        }

        [, , $output, $fileName] = $this->buildPdfCliente($cotizacion->id);

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    private function buildPdfCliente(int $id): array
    {
        $cotizacion = Cotizacion::with([
            'cliente',
            'creadoPor',
            'detallesCotizacion.tela',
            'detallesCotizacion.tergal',
            'detallesCotizacion.forro',
            'insumos.tipoInsumo',
            'productos.tipoProducto',
        ])->findOrFail($id);

        $detalles = $cotizacion->detallesCotizacion;
        if ($detalles->isEmpty() && $cotizacion->detalleCotizacion) {
            $detalles = collect([$cotizacion->detalleCotizacion]);
        }

        $output = Pdf::loadView('admin.cotizaciones.pdf', compact('cotizacion', 'detalles'))->output();
        $fileName = 'cotizacion_' . $cotizacion->id . '.pdf';
        Storage::disk('public')->put('pdfs/' . $fileName, $output);

        return [$cotizacion, $detalles, $output, $fileName];
    }

    public function pdfDecorador(Cotizacion $cotizacion)
    {
        $cotizacion->load([
            'cliente',
            'creadoPor',
            'detallesCotizacion.tela',
            'detallesCotizacion.tergal',
            'detallesCotizacion.forro',
            'insumos.tipoInsumo',
            'productos.tipoProducto',
        ]);

        $detalles = $cotizacion->detallesCotizacion;
        if ($detalles->isEmpty() && $cotizacion->detalleCotizacion) {
            $detalles = collect([$cotizacion->detalleCotizacion]);
        }

        $pdf = Pdf::loadView('admin.cotizaciones.pdfdecorador', compact('cotizacion', 'detalles'));
        return $pdf->stream('decorador_'.$cotizacion->id.'.pdf');
    }

    private function mapProductosParaCotizacion()
    {
        return Producto::with(['tipoProducto', 'insumos.proveedor'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) {
                $tipoNombre = strtolower($producto->tipoProducto->nombre ?? '');
                $esCortinero = (int) $producto->id_tipo_producto === 1 || $tipoNombre === 'cortinero';

                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'clave' => $producto->clave,
                    'etiqueta' => $producto->etiquetaCotizacion(),
                    'precio' => $producto->precio,
                    'precio_publico' => $producto->precio_publico ?? $producto->precio,
                    'id_tipo_producto' => $producto->id_tipo_producto,
                    'tipo_nombre' => $producto->tipoProducto->nombre ?? '',
                    'es_cortinero' => $esCortinero,
                    'insumos' => $esCortinero
                        ? $producto->insumos->map(function ($insumo) {
                            return [
                                'nombre' => $insumo->etiquetaClaveNombre(),
                                'cantidad' => (float) ($insumo->pivot->cantidad ?? 0),
                            ];
                        })->values()->all()
                        : [],
                ];
            })
            ->values();
    }
}
