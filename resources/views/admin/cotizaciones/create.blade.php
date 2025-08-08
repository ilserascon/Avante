@extends('layouts.stisla')

@section('title', 'Nueva Cotización')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Nueva Cotización</h1>
    </div>

    <div class="section-body">
        <form method="POST" action="{{ route('admin.cotizaciones.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control" required autocomplete="off">
                        <option value="">Seleccione un cliente</option>
                        @foreach(\App\Models\Cliente::where('borrado', 0)->orderBy('nombre')->get() as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="tipo_cortina" class="form-label">Tipo de Cortina</label>
                    <input type="text" name="detalle[tipo_cortina]" id="tipo_cortina" class="form-control" placeholder="Ejemplo: plisada, rizada, wave">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="area" class="form-label">Área</label>
                    <input type="text" name="area" id="area" class="form-control" placeholder="Ejemplo: Cocina, Habitación, etc." value="{{ old('area', $cotizacion->area ?? '') }}">
                </div>
            </div>
            <div class="row mb-3 align-items-center">
                <div class="col d-flex align-items-center gap-3">
                    <label class="mb-0 me-3 align-middle" style="vertical-align: middle;">Tipo de Cotización:</label>
                    <div class="form-check form-check-inline" style="margin-left: 0.5rem;">
                        <input type="checkbox" id="cortinaCheck" name="tipo[]" value="cortina" class="form-check-input" autocomplete="off">
                        <label class="form-check-label" for="cortinaCheck">Cortina</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="tergalCheck" name="tipo[]" value="tergal" class="form-check-input" autocomplete="off">
                        <label class="form-check-label" for="tergalCheck">Tergal</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="forroCheck" name="lleva_forro" value="1" class="form-check-input" autocomplete="off">
                        <label class="form-check-label" for="forroCheck">Lleva Forro</label>
                    </div>
                </div>
            </div>

            <div id="form-dinamico" class="mb-4">
                <!-- Formularios dinámicos -->
            </div>

            <!-- Tabla Totales Tela, Tergal y Forro -->
            <div class="card mt-4" id="tabla-totales-tela-tergal">
                <div class="card-header">
                    <h4>Totales Tela, Tergal y Forro</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Total Tela, Tergal y Forro</th>
                                    <th>Precio m²</th>
                                    <th>Descripción</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Fila Cortina -->
                                <tr>
                                    <td>
                                        <input type="number" name="detalle[total_tela]" id="total_tela" class="form-control" step="0.01"
                                            value="{{ old('detalle.total_tela', $detalleCotizacion->total_tela ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[precio_m2_tela]" id="precio_m2_tela" class="form-control" step="0.01"
                                                value="{{ old('detalle.precio_m2_tela', $detalleCotizacion->precio_m2_tela ?? '100.00') }}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="detalle[descripcion_tela]" class="form-control" placeholder="Cortina"
                                            value="{{ old('detalle.descripcion_tela', $detalleCotizacion->descripcion_tela ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_tela_final]" id="total_tela_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_tela_final', $detalleCotizacion->total_tela_final ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Tergal -->
                                <tr>
                                    <td>
                                        <input type="number" name="detalle[total_tergal]" id="total_tergal" class="form-control" step="0.01"
                                            value="{{ old('detalle.total_tergal', $detalleCotizacion->total_tergal ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[precio_m2_tergal]" id="precio_m2_tergal" class="form-control" step="0.01"
                                                value="{{ old('detalle.precio_m2_tergal', $detalleCotizacion->precio_m2_tergal ?? '70.00') }}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="detalle[descripcion_tergal]" class="form-control" placeholder="Tergal"
                                            value="{{ old('detalle.descripcion_tergal', $detalleCotizacion->descripcion_tergal ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_tergal_final]" id="total_tergal_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_tergal_final', $detalleCotizacion->total_tergal_final ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Forro -->
                                <tr>
                                    <td>
                                        <input type="number" id="total_forro" name="detalle[total_forro]" class="form-control" step="0.01"
                                            value="{{ old('detalle.total_forro', $detalleCotizacion->total_forro ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[precio_m2_forro]" id="precio_m2_forro" class="form-control" step="0.01"
                                                value="{{ old('detalle.precio_m2_forro', $detalleCotizacion->precio_m2_forro ?? '35.00') }}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="detalle[descripcion_forro]" class="form-control" placeholder="Forro"
                                            value="{{ old('detalle.descripcion_forro', $detalleCotizacion->descripcion_forro ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_final_forro]" id="total_final_forro" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_final_forro', $detalleCotizacion->total_final_forro ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <!-- Total general -->
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Costo total tela, tergal y forro:</strong></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[costo_total_tela_tergal_forro]" id="costo_total_tela_tergal_forro" class="form-control" step="0.01"
                                                value="{{ old('detalle.costo_total_tela_tergal_forro', $detalleCotizacion->costo_total_tela_tergal_forro ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabla Mano de Obra -->
            <div class="card mt-4 d-none" id="tabla-mano-obra">
                <div class="card-header">
                    <h4>Mano de Obra</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>m²</th>
                                    <th>Costo Mano de Obra</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <label for="m2_1" class="me-2 mb-0" style="margin-right: 0.6rem;">Cortina</label>
                                            <input type="number" name="detalle[m2_1]" class="form-control" step="0.01"
                                                value="{{ old('detalle.m2_1', $detalleCotizacion->m2_1 ?? '') }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                name="detalle[costo_mano_obra_1]"
                                                class="form-control"
                                                step="0.01"
                                                value="{{ old('detalle.costo_mano_obra_1', $detalleCotizacion->costo_mano_obra_1 ?? ($manoObra['Mano de Obra Cortina']->precio_publico ?? '')) }}"
                                                readonly>
                                            <input type="hidden" id="valor_base_mano_obra"
                                                value="{{ $manoObra['Mano de Obra Cortina']->precio_publico ?? 120 }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_mano_obra_1]" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_mano_obra_1', $detalleCotizacion->total_mano_obra_1 ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <label for="m2_2" class="me-2 mb-0" style="margin-right: 1rem;">Tergal</label>
                                            <input type="number" name="detalle[m2_2]" id="m2_2" class="form-control" step="0.01"
                                                value="{{ old('detalle.m2_2', $detalleCotizacion->m2_2 ?? '') }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                name="detalle[costo_mano_obra_2]"
                                                class="form-control"
                                                step="0.01"
                                                value="{{ old('detalle.costo_mano_obra_2', $detalleCotizacion->costo_mano_obra_2 ?? ($manoObra['Mano de Obra Tergal']->precio_publico ?? '')) }}"
                                                readonly>
                                            <input type="hidden" id="valor_base_mano_obra_tergal"
                                                value="{{ $manoObra['Mano de Obra Tergal']->precio_publico ?? 100 }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_mano_obra_2]" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_mano_obra_2', $detalleCotizacion->total_mano_obra_2 ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Costo Total Mano de Obra:</strong></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[costo_total_mano_obra]" class="form-control" step="0.01"
                                                value="{{ old('detalle.costo_total_mano_obra', $detalleCotizacion->costo_total_mano_obra ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabla Materiales Varios -->
            <div class="card mt-4 d-none" id="tabla-materiales-varios">
                <div class="card-header">
                    <h4>Materiales Varios</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 180px;">Materiales Varios</th>
                                    <th style="min-width: 120px;">Cantidad</th>
                                    <th style="min-width: 150px;">Precio Unitario</th>
                                    <th style="min-width: 150px;">Subtotal</th>
                                    <th style="min-width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="materiales-tbody">
                                <!-- Insumos fijos -->
                                <tr>
                                    <td>
                                        Cortinero cortina
                                        <select name="detalle[cortinero_id]" id="cortinero_id" class="form-select select2">
                                            <option value="">Seleccione tipo de cortinero</option>
                                            @foreach($cortineros as $cortinero)
                                            <option value="{{ $cortinero->id }}" data-precio="{{ $cortinero->precio_publico }}">
                                                {{ $cortinero->nombre }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[cortinero_cantidad]" id="cortinero_cantidad" class="form-control" oninput="calcularSubtotalCortinero()" autocomplete="off">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_precio" name="detalle[cortinero_precio]" class="form-control" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_subtotal" class="form-control" readonly step="0.01">
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        Cortinero tergal
                                        <select name="detalle[cortinero_tergal_id]" id="cortinero_tergal_id" class="form-select select2">
                                            <option value="">Seleccione tipo de cortinero</option>
                                            @foreach($cortineros as $cortinero)
                                            <option value="{{ $cortinero->id }}" data-precio="{{ $cortinero->precio_publico }}">
                                                {{ $cortinero->nombre }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[cortinero_tergal_cantidad]" id="cortinero_tergal_cantidad" class="form-control" oninput="calcularSubtotalCortineroTergal()" autocomplete="off">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_tergal_precio" name="detalle[cortinero_tergal_precio]" class="form-control" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_tergal_subtotal" class="form-control" readonly step="0.01">
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Cortinero cortina
                                        $('#cortinero_id').select2();
                                        const cortineroSelect = $('#cortinero_id');
                                        const cortineroPrecio = document.getElementById('cortinero_precio');
                                        const cortineroCantidad = document.getElementById('cortinero_cantidad');
                                        const cortineroSubtotal = document.getElementById('cortinero_subtotal');
                                        function calcularSubtotalCortinero() {
                                            const cantidad = parseFloat(cortineroCantidad.value) || 0;
                                            const precio = parseFloat(cortineroPrecio.value) || 0;
                                            cortineroSubtotal.value = (cantidad * precio).toFixed(2);
                                            actualizarCostoTotal();
                                        }
                                        cortineroSelect.on('change', function() {
                                            const selected = $(this).find('option:selected');
                                            cortineroPrecio.value = selected.data('precio') || '';
                                            calcularSubtotalCortinero();
                                            actualizarTablaTotales();
                                        });
                                        cortineroCantidad.addEventListener('input', calcularSubtotalCortinero);
                                        // Inicializa el precio y subtotal al cargar
                                        cortineroPrecio.value = cortineroSelect.find('option:selected').data('precio') || '';
                                        calcularSubtotalCortinero();

                                        // Cortinero tergal
                                        $('#cortinero_tergal_id').select2();
                                        const cortineroTergalSelect = $('#cortinero_tergal_id');
                                        const cortineroTergalPrecio = document.getElementById('cortinero_tergal_precio');
                                        const cortineroTergalCantidad = document.getElementById('cortinero_tergal_cantidad');
                                        const cortineroTergalSubtotal = document.getElementById('cortinero_tergal_subtotal');
                                        function calcularSubtotalCortineroTergal() {
                                            const cantidad = parseFloat(cortineroTergalCantidad.value) || 0;
                                            const precio = parseFloat(cortineroTergalPrecio.value) || 0;
                                            cortineroTergalSubtotal.value = (cantidad * precio).toFixed(2);
                                            actualizarCostoTotal();
                                        }
                                        cortineroTergalSelect.on('change', function() {
                                            const selected = $(this).find('option:selected');
                                            cortineroTergalPrecio.value = selected.data('precio') || '';
                                            calcularSubtotalCortineroTergal();
                                            actualizarTablaTotales();
                                        });
                                        cortineroTergalCantidad.addEventListener('input', calcularSubtotalCortineroTergal);
                                        // Inicializa el precio y subtotal al cargar
                                        cortineroTergalPrecio.value = cortineroTergalSelect.find('option:selected').data('precio') || '';
                                        calcularSubtotalCortineroTergal();

                                        // Expone funciones globales para insumos dinámicos
                                        window.calcularSubtotalCortinero = calcularSubtotalCortinero;
                                        window.calcularSubtotalCortineroTergal = calcularSubtotalCortineroTergal;
                                    });
                                </script>
                                <!-- Aquí se insertarán los insumos dinámicos -->
                            </tbody>
                            <tfoot>
                                <tr id="row-boton-otro-insumo">
                                    <td colspan="4" class="text-start">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="añadirOtroInsumo()">Añadir otro</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Costo Total Materiales:</strong></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[costo_total_materiales]" id="costo_total_materiales" class="form-control" readonly>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Totales -->
            <div class="card mt-4" id="tabla-totales">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Totales</h4>
                </div>
                <div class="card-body pt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered mb-0 align-middle">
                                <tbody>
                                    <tr>
                                        <td><strong>Total No. Lienzos</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_lienzos" name="totales[total_lienzos]" value="{{ old('totales.total_lienzos', $cotizacion->total_lienzos ?? '') }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Forro</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_forro" name="totales[total_m2_forro]" value="{{ old('totales.total_m2_forro', $cotizacion->total_m2_forro ?? '') }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Tela</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_tela" name="totales[total_m2_tela]" value="{{ old('totales.total_m2_tela', $cotizacion->total_m2_tela ?? '') }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Tergal</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_tergal" name="totales[total_m2_tergal]" value="{{ old('totales.total_m2_tergal', $cotizacion->total_m2_tergal ?? '') }}" readonly>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered mb-0 align-middle">
                                <tbody>
                                    <tr>
                                        <td><strong>Costo Cortina</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="costo_cortina" name="totales[costo_cortina]" value="{{ old('totales.costo_cortina', $cotizacion->costo_cortina ?? '') }}" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $esAdmin = auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador';
                                    @endphp
                                    @if($esAdmin)
                                        <tr>
                                            <td><strong>Utilidad</strong></td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="utilidad" name="totales[utilidad]" value="{{ old('totales.utilidad', $cotizacion->utilidad ?? '') }}" readonly>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Costo Decorador</strong></td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number"
                                                        id="decorador_porcentaje"
                                                        name="totales[decorador_porcentaje]"
                                                        class="form-control text-end"
                                                        value="{{ old('totales.decorador_porcentaje', $detalleCotizacion->decorador_porcentaje ?? 15) }}"
                                                        min="0" max="100" step="0.01"
                                                        style="max-width: 100px;">
                                                    <span class="input-group-text">%</span>
                                                    <span class="input-group-text" style="margin-left: 0.5rem;">$</span>
                                                    <input type="number" class="form-control" id="costo_decorador" name="totales[costo_decorador]" value="{{ old('totales.costo_decorador', $cotizacion->costo_decorador ?? '') }}" readonly>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- Campos ocultos para cotizador --}}
                                        <input type="hidden" id="utilidad" name="totales[utilidad]" value="{{ old('totales.utilidad', $cotizacion->utilidad ?? '') }}">
                                        <input type="hidden" id="costo_decorador" name="totales[costo_decorador]" value="{{ old('totales.costo_decorador', $cotizacion->costo_decorador ?? '') }}">
                                    @endif
                                    <tr>
                                        <td><strong>Precio Público</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="precio_publico" name="totales[precio_publico]" value="{{ old('totales.precio_publico', $cotizacion->precio_publico ?? '') }}" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <div class="row justify-content-end">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" value="1" id="aplicar_iva" name="aplicar_iva">
                                                        <label class="form-check-label" for="aplicar_iva">
                                                            Aplicar IVA (16%)
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="descuento" class="form-label mb-0">Descuento (%)</label>
                                                    <input type="number" class="form-control" id="descuento" name="descuento" min="0" max="100" step="0.01" value="0">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-4">Guardar Cotización</button>
            <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary mt-4">Cancelar</a>
        </form>
    </div>
</div>

<select id="plantilla_tela" class="d-none">
    @php
    function limpiarPrecio($valor) {
    $valor = str_replace(['$', ' '], '', $valor);
    $valor = str_replace(',', '.', $valor);
    return floatval($valor);
    }
    @endphp
    <option value="">Seleccione una tela</option>
    @foreach($telas as $tela)
    @php
    if(limpiarPrecio($tela->precio_publico) > 0) {
    $precio = limpiarPrecio($tela->precio_publico);
    } elseif(limpiarPrecio($tela->campo6) > 0) {
    $precio = limpiarPrecio($tela->campo6);
    } elseif(limpiarPrecio($tela->campo13) > 0) {
    $precio = limpiarPrecio($tela->campo13);
    } else {
    $precio = 100;
    }
    @endphp
    <option value="{{ $tela->id }}" data-precio="{{ $precio }}" data-campo2="{{ $tela->campo2 }}">
        {{ $tela->nombre }} - {{ $tela->campo1 }} - {{ $tela->campo2 }}
    </option>
    @endforeach
</select>

<select id="plantilla_tergal" class="d-none">
    <option value="">Seleccione un tergal</option>
    @foreach($tergales as $tergal)
        <option
            value="{{ $tergal->id }}"
            data-precio="{{ is_numeric($tergal->precio_publico) ? $tergal->precio_publico : 0 }}"
            data-campo1="{{ $tergal->campo1 }}"
            data-campo2="{{ $tergal->campo2 }}"
        >
            {{ $tergal->nombre }} - {{ $tergal->campo1 }} - {{ $tergal->campo2 }}
        </option>
    @endforeach
</select>


<select id="plantilla_forro" class="d-none">
    <option value="">Seleccione un forro</option>
    @foreach($forros as $forro)
    <option
        value="{{ $forro->id }}"
        data-precio="{{ is_numeric($forro->precio_publico) ? $forro->precio_publico : 0 }}"
        data-campo1="{{ $forro->campo1 }}"
        data-campo2="{{ $forro->campo2 }}">
        {{ $forro->nombre }} - {{ $forro->campo1 }} - {{ $forro->campo2 }}
    </option>
    @endforeach
</select>

<script>
    //Script para mostrar y ocultar formularios dinámicos
    document.addEventListener('DOMContentLoaded', function() {
        const cortina = document.getElementById('cortinaCheck');
        const tergal = document.getElementById('tergalCheck');
        const forro = document.getElementById('forroCheck');
        const formDinamico = document.getElementById('form-dinamico');

        function actualizarFormulario() {
            const valoresPrevios = {};
            const atributosOriginales = {};
            const estadosCheckbox = {};

            // Guardar valores para que no se borren al actualizar el formulario
            let telaSeleccionada = null;
            let tergalSeleccionado = null;
            let forroSeleccionado = null;

            const telaSelectExistente = document.getElementById('tela_id');
            if (telaSelectExistente) telaSeleccionada = telaSelectExistente.value;

            const tergalSelectExistente = document.getElementById('tergal_id');
            if (tergalSelectExistente) tergalSeleccionado = tergalSelectExistente.value;

            const forroSelectExistente = document.getElementById('forro_id');
            if (forroSelectExistente) forroSeleccionado = forroSelectExistente.value;

            formDinamico.querySelectorAll('input').forEach(input => {
                if (input.name) valoresPrevios[input.name] = input.value;
                if (input.type === 'checkbox' && input.id) estadosCheckbox[input.id] = input.checked;
                if (input.dataset && input.dataset.original !== undefined && input.id) atributosOriginales[input.id] = input.dataset.original;
            });

            formDinamico.innerHTML = '';

                        if (cortina.checked) {
                            formDinamico.innerHTML += `
                                <div class="card mt-4">
                                    <div class="card-header pb-1">
                                        <h4 class="mb-1">Datos de la Cortina</h4>
                                    </div>
                                    <div class="card-body pt-2">
                                        <!-- Select Tela fuera de la tabla -->
                                        <div class="mb-3">
                                            <label for="tela_id" class="form-label">Tela</label>
                                            <select id="tela_id" name="detalle[tela_id]" class="form-control select2" required
                                                oninvalid="this.setCustomValidity('Por favor selecciona una tela')"
                                                oninput="this.setCustomValidity('')">
                                                <option value="">Seleccione una tela</option>
                                                @foreach($telas as $tela)
                                                    <option value="{{ $tela->id }}"
                                                        {{ old('detalle.tela_id', $detalleCotizacion->tela_id ?? '') == $tela->id ? 'selected' : '' }}>
                                                        {{ $tela->nombre }} - {{ $tela->campo1 }} - {{ $tela->campo2 }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Ancho tela (cm)</th>
                                                        <th>Ancho cortina (cm)</th>
                                                        <th>Largo (m)</th>
                                                        <th>No. Lienzos</th>
                                                        <th>No. Lienzos Redondeados</th>
                                                        <th>Bastilla (m)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="detalle[ancho_tela]" id="ancho_tela" class="form-control"
                                                                value="{{ old('detalle.ancho_tela', $detalleCotizacion->ancho_tela ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[ancho]" id="ancho" class="form-control"
                                                                value="{{ old('detalle.ancho', $detalleCotizacion->ancho ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[largo]" id="largo" class="form-control"
                                                                value="{{ old('detalle.largo', $detalleCotizacion->largo ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos]" id="no_lienzos" class="form-control"
                                                                value="{{ old('detalle.no_lienzos', $detalleCotizacion->no_lienzos ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_redondeado]" id="no_lienzos_redondeado" class="form-control"
                                                                value="{{ old('detalle.no_lienzos_redondeado', $detalleCotizacion->no_lienzos_redondeado ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" id="valor_bastilla" name="detalle[valor_bastilla]" class="form-control"
                                                                value="{{ old('detalle.valor_bastilla', $detalleCotizacion->bastilla ?? .40) }}"
                                                                placeholder="Ej. 0.40m" step="0.01" min="0">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            setTimeout(function() {
                                const plantilla = document.getElementById('plantilla_tela');
                                const telaSelect = document.getElementById('tela_id');
                                telaSelect.innerHTML = plantilla.innerHTML;

                                // Restaurar selección antes de select2
                                if (telaSeleccionada) {
                                    $(telaSelect).val(telaSeleccionada);
                                }

                                $(telaSelect).select2();

                                $(telaSelect).on('change', function() {
                                    const selected = $(this).find('option:selected');
                                    const precio = selected.data('precio');
                                    const campo2 = selected.data('campo2');
                                    $('#precio_m2_tela').val(Number(precio).toFixed(2)).trigger('input');
                                    if (campo2 !== undefined && campo2 !== null && campo2 !== '') {
                                        // Limpia el valor para dejar solo números y punto decimal
                                        let limpio = campo2.toString().replace(/[^\d.]/g, '');
                                        $('#ancho_tela').val(limpio);
                                    }

                                    const metros = parseFloat($('#total_tela').val()) || 0;
                                    const total = metros * Number(precio);
                                    $('#total_tela_final').val(total.toFixed(2));

                                    const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
                                    const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((total + totalTergalFinal + totalForroFinal).toFixed(2));

                                    actualizarTablaTotales();
                                    calcularLienzos();
                                    actualizarPrecioManoObra();
                                });

                                $(document).on('change', '#tela_id', function() {
                                    const precio = $(this).find('option:selected').data('precio');
                                    $('#precio_m2_tela').val(Number(precio).toFixed(2));

                                    // Recalcular total_tela (m²) al cambiar la tela
                                    const noLienzosCortina = parseFloat($('#no_lienzos_redondeado').val()) || 0;
                                    const largoCortina = parseFloat($('#largo').val()) || 0;
                                    const bastillaCortina = parseFloat($('#valor_bastilla').val()) || 0;
                                    const totalTela = noLienzosCortina * (largoCortina + bastillaCortina);
                                    $('#total_tela').val(totalTela.toFixed(2));

                                    // Calcular total final de tela
                                    const total = totalTela * Number(precio);
                                    $('#total_tela_final').val(total.toFixed(2));

                                    // Actualizar costo total combinado
                                    const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
                                    const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((total + totalTergalFinal + totalForroFinal).toFixed(2));

                                    // Actualizar tabla de totales
                                    actualizarTablaTotales();
                                });

                                $(telaSelect).trigger('change');
                            }, 0);
                        }

                        if (tergal.checked) {
                            formDinamico.innerHTML += `
                                <div class="card mt-4">
                                    <div class="card-header pb-1">
                                        <h4 class="mb-1">Datos del Tergal</h4>
                                    </div>
                                    <div class="card-body pt-2">
                                        <!-- Select Tergal fuera de la tabla -->
                                        <div class="mb-3">
                                            <label for="tergal_id" class="form-label">Tergal</label>
                                            <select id="tergal_id" name="detalle[tergal_id]" class="form-control select2" required
                                                oninvalid="this.setCustomValidity('Por favor selecciona un tergal')"
                                                oninput="this.setCustomValidity('')">
                                                <option value="">Seleccione un tergal</option>
                                                @foreach($tergales as $tergal)
                                                    <option value="{{ $tergal->id }}"
                                                        data-precio="{{ $tergal->precio ?? 0 }}"
                                                        data-campo1="{{ $tergal->campo1 ?? '' }}"
                                                        data-campo2="{{ $tergal->campo2 ?? '' }}"
                                                        {{ old('detalle.tergal_id', $detalleCotizacion->tergal_id ?? '') == $tergal->id ? 'selected' : '' }}>
                                                        {{ $tergal->nombre }} - {{ $tergal->campo1 ?? '' }} - {{ $tergal->campo2 ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Ancho tela (cm)</th>
                                                        <th>Ancho tergal (cm)</th>
                                                        <th>Largo (m)</th>
                                                        <th>No. Lienzos</th>
                                                        <th>No. Lienzos Redondeados</th>
                                                        <th>Bastilla (m)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="detalle[ancho_tergal]" id="ancho_tergal" class="form-control"
                                                                value="{{ old('detalle.ancho_tergal', $detalleCotizacion->ancho_tergal ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[ancho_tergal_real]" id="ancho_tergal_real" class="form-control"
                                                                value="{{ old('detalle.ancho_tergal_real', $detalleCotizacion->ancho_tergal_real ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[largo_tergal]" id="largo_tergal" class="form-control"
                                                                value="{{ old('detalle.largo_tergal', $detalleCotizacion->largo_tergal ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_tergal]" id="no_lienzos_tergal" class="form-control"
                                                                value="{{ old('detalle.no_lienzos_tergal', $detalleCotizacion->no_lienzos_tergal ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_redondeado_tergal]" id="no_lienzos_redondeado_tergal" class="form-control"
                                                                value="{{ old('detalle.no_lienzos_redondeado_tergal', $detalleCotizacion->no_lienzos_redondeado_tergal ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" id="valor_bastilla_tergal" name="detalle[valor_bastilla_tergal]" class="form-control"
                                                                value="{{ old('detalle.valor_bastilla_tergal', $detalleCotizacion->bastilla_tergal ?? 0.65) }}"
                                                                placeholder="Ej. 0.65m" step="0.01" min="0">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            setTimeout(() => {
                                const plantillaTergal = document.getElementById('plantilla_tergal');
                                const tergalSelect = document.getElementById('tergal_id');
                                const anchoTergalInput = document.getElementById('ancho_tergal');
                                const anchoTergalRealInput = document.getElementById('ancho_tergal_real');
                                const largoTergalInput = document.getElementById('largo_tergal');
                                const noLienzosTergalInput = document.getElementById('no_lienzos_tergal');
                                const noLienzosRedondeadoTergalInput = document.getElementById('no_lienzos_redondeado_tergal');

                                tergalSelect.innerHTML = plantillaTergal.innerHTML;

                                if (typeof tergalSeleccionado !== 'undefined' && tergalSeleccionado) {
                                    $(tergalSelect).val(tergalSeleccionado);
                                }

                                $(tergalSelect).select2();

                                // Calcular lienzos tergal
                                function calcularTergal() {
                                    const anchoReal = parseFloat(anchoTergalRealInput.value);
                                    const anchoTela = parseFloat(anchoTergalInput.value);

                                    if (!isNaN(anchoReal) && !isNaN(anchoTela) && anchoTela > 0) {
                                        let lienzos = (anchoReal * 2.5) / anchoTela;
                                        noLienzosTergalInput.value = lienzos.toFixed(2);
                                        noLienzosRedondeadoTergalInput.value = Math.ceil(lienzos);
                                    } else {
                                        noLienzosTergalInput.value = '';
                                        noLienzosRedondeadoTergalInput.value = '';
                                    }
                                }

                                largoTergalInput.addEventListener('blur', () => {
                                    let val = parseFloat(largoTergalInput.value);
                                    if (!isNaN(val)) {
                                        largoTergalInput.value = val.toFixed(2);
                                    }
                                });

                                $(tergalSelect).on('change', function () {
                                    const selected = $(this).find('option:selected');
                                    const campo1 = selected.data('campo1');

                                    if (campo1 !== undefined && campo1 !== null && campo1 !== '') {
                                        let limpio = campo1.toString().replace(/[^\d.]/g, '');
                                        anchoTergalInput.value = limpio;
                                    }

                                    const precio = selected.data('precio');
                                    $('#precio_m2_tergal').val(Number(precio).toFixed(2)).trigger('input');

                                    const metros = parseFloat($('#total_tergal').val()) || 0;
                                    const total = metros * Number(precio);
                                    $('#total_tergal_final').val(total.toFixed(2));

                                    const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
                                    const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((totalTelaFinal + total + totalForroFinal).toFixed(2));

                                    actualizarTablaTotales();

                                    sincronizarTergalConCortina();
                                    actualizarPrecioManoObra();


                                    calcularTergal();
                                });

                                function sincronizarTergalConCortina() {
                                    const anchoCortina = document.getElementById('ancho');
                                    const largoCortina = document.getElementById('largo');
                                    const anchoTelaCortina = document.getElementById('ancho_tela');

                                    if (anchoCortina?.value && anchoTelaCortina?.value) {
                                        anchoTergalRealInput.value = anchoCortina.value;

                                        if (largoCortina && largoCortina.value) {
                                            largoTergalInput.value = parseFloat(largoCortina.value).toFixed(2);
                                        } else {
                                            largoTergalInput.value = '';
                                        }
                                    }
                                }


                                ['ancho', 'largo', 'ancho_tela'].forEach(id => {
                                    const input = document.getElementById(id);
                                    if (input) {
                                        input.addEventListener('input', () => {
                                            sincronizarTergalConCortina();
                                            calcularTergal();
                                            $(tergalSelect).trigger('change');
                                        });
                                    }
                                });

                                [anchoTergalRealInput, anchoTergalInput].forEach(input => {
                                    input.addEventListener('input', calcularTergal);
                                });

                                sincronizarTergalConCortina();
                                calcularTergal();

                                $(tergalSelect).trigger('change');

                            }, 200);
                        }

                        if (forro.checked) {
                            formDinamico.innerHTML += `
                                <div class="card mt-4">
                                    <div class="card-header pb-1">
                                        <h4 class="mb-1">Datos del Forro</h4>
                                    </div>
                                    <div class="card-body pt-2">
                                        <!-- Select Forro fuera de la tabla -->
                                        <div class="mb-3">
                                            <label for="forro_id" class="form-label">Forro</label>
                                            <select id="forro_id" name="detalle[forro_id]" class="form-control select2" required
                                                oninvalid="this.setCustomValidity('Por favor selecciona un forro')"
                                                oninput="this.setCustomValidity('')">
                                                <option value="">Seleccione un forro</option>
                                                @foreach($forros as $forro)
                                                    <option value="{{ $forro->id }}"
                                                        {{ old('detalle.forro_id', $detalleCotizacion->forro_id ?? '') == $forro->id ? 'selected' : '' }}>
                                                        {{ $forro->nombre }} - {{ $forro->campo1 ?? '' }} - {{ $forro->campo2 ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Ancho tela (cm)</th>
                                                        <th>Ancho forro (cm)</th>
                                                        <th>Largo (m)</th>
                                                        <th>No. Lienzos</th>
                                                        <th>No. Lienzos Redondeados</th>
                                                        <th>Bastilla (m)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="number" name="detalle[ancho_forro]" id="ancho_forro" class="form-control"
                                                                value="{{ old('detalle.ancho_forro', $detalleCotizacion->ancho_forro ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[ancho_forro_real]" id="ancho_forro_real" class="form-control"
                                                                value="{{ old('detalle.ancho_forro_real', $detalleCotizacion->ancho_forro_real ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[largo_forro]" id="largo_forro" class="form-control"
                                                                value="{{ old('detalle.largo_forro', $detalleCotizacion->largo_forro ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_forro]" id="no_lienzos_forro" class="form-control"
                                                                value="{{ old('detalle.no_lienzos_forro', $detalleCotizacion->no_lienzos_forro ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_redondeado_forro]" id="no_lienzos_redondeado_forro" class="form-control"
                                                                value="{{ old('detalle.no_lienzos_redondeado_forro', $detalleCotizacion->no_lienzos_redondeado_forro ?? '') }}" step="0.01" min="0">
                                                        </td>
                                                        <td>
                                                            <input type="number" id="valor_bastilla_forro" name="detalle[valor_bastilla_forro]" class="form-control"
                                                                value="{{ old('detalle.valor_bastilla_forro', $detalleCotizacion->bastilla_forro ?? 0.40) }}"
                                                                placeholder="Ej. 0.40m" step="0.01" min="0">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            setTimeout(() => {
                                const plantillaForro = document.getElementById('plantilla_forro');
                                const forroSelect = document.getElementById('forro_id');
                                const anchoForroRealInput = document.getElementById('ancho_forro_real');
                                const largoForroInput = document.getElementById('largo_forro');
                                const anchoForroInput = document.getElementById('ancho_forro');
                                const noLienzosForroInput = document.getElementById('no_lienzos_forro');
                                const noLienzosRedondeadoForroInput = document.getElementById('no_lienzos_redondeado_forro');

                                forroSelect.innerHTML = plantillaForro.innerHTML;

                                if (typeof forroSeleccionado !== 'undefined' && forroSeleccionado) {
                                    $(forroSelect).val(forroSeleccionado);
                                }

                                $(forroSelect).select2();

                                largoForroInput.addEventListener('blur', () => {
                                    let val = parseFloat(largoForroInput.value);
                                    if (!isNaN(val)) {
                                        largoForroInput.value = val.toFixed(2);
                                    }
                                });

                                function calcularForro() {
                                    const anchoReal = parseFloat(anchoForroRealInput.value);
                                    const anchoTela = parseFloat(anchoForroInput.value);

                                    if (!isNaN(anchoReal) && !isNaN(anchoTela) && anchoTela > 0) {
                                        let lienzos = (anchoReal * 2.5) / anchoTela;
                                        noLienzosForroInput.value = lienzos.toFixed(2);
                                        noLienzosRedondeadoForroInput.value = Math.ceil(lienzos);
                                    } else {
                                        noLienzosForroInput.value = '';
                                        noLienzosRedondeadoForroInput.value = '';
                                    }
                                }

                                function sincronizarDimensionesForro() {
                                    const anchoCortina = document.getElementById('ancho')?.value;
                                    const largoCortina = document.getElementById('largo')?.value;

                                    const anchoTergal = document.getElementById('ancho_tergal_real')?.value;
                                    const largoTergal = document.getElementById('largo_tergal')?.value;

                                    let anchoBase = anchoCortina || anchoTergal || '';
                                    let largoBase = largoCortina || largoTergal || '';

                                    if (anchoBase) {
                                        anchoForroRealInput.value = anchoBase;
                                    }
                                    if (largoBase) {
                                        largoForroInput.value = parseFloat(largoBase).toFixed(2);
                                    }

                                    calcularForro();
                                    const changeEvent = new Event('change', { bubbles: true });
                                    document.dispatchEvent(changeEvent);
                                }

                                $(forroSelect).on('change', function () {
                                    const selected = $(this).find('option:selected');
                                    const campo1 = selected.text().split('-')[1]?.trim();

                                    if (campo1 !== undefined && campo1 !== null && campo1 !== '') {
                                        let limpio = campo1.toString().replace(/[^\d.]/g, '');
                                        anchoForroInput.value = limpio;
                                    }

                                    const precio = selected.data('precio');
                                    $('#precio_m2_forro').val(Number(precio).toFixed(2)).trigger('input');

                                    const metros = parseFloat($('#total_forro').val()) || 0;
                                    const total = metros * Number(precio);
                                    $('#total_final_forro').val(total.toFixed(2));

                                    const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
                                    const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((totalTelaFinal + totalTergalFinal + total).toFixed(2));

                                    actualizarTablaTotales();
                                });

                                ['ancho', 'largo', 'ancho_tergal_real', 'largo_tergal'].forEach(id => {
                                    const input = document.getElementById(id);
                                    if (input) {
                                        input.addEventListener('input', sincronizarDimensionesForro);
                                    }
                                });

                                [anchoForroRealInput, anchoForroInput].forEach(input => {
                                    if (input) {
                                        input.addEventListener('input', calcularForro);
                                    }
                                });

                                sincronizarDimensionesForro();

                                $(forroSelect).trigger('change');
                            }, 200);
                        }

            // Restaura valores guardados
            const nuevosInputs = formDinamico.querySelectorAll('input');
            nuevosInputs.forEach(input => {
                if (input.name && valoresPrevios.hasOwnProperty(input.name)) {
                    input.value = valoresPrevios[input.name];
                }
                if (input.type === 'number' && estadosCheckbox.hasOwnProperty(input.id)) {
                    input.value = estadosCheckbox[input.id];
                }

                if (input.dataset && atributosOriginales.hasOwnProperty(input.id)) {
                    input.dataset.original = atributosOriginales[input.id];
                }
            });
            const bastillaTergalInput = document.getElementById('valor_bastilla_tergal');
            if (bastillaTergalInput && bastillaTergalInput.value !== '') {
                bastillaTergalInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            }

        }


        cortina.addEventListener('change', actualizarFormulario);
        tergal.addEventListener('change', actualizarFormulario);
        forro.addEventListener('change', actualizarFormulario);
    });

    // Función auxiliar para obtener float seguro
    function parseSafeFloat(value) {
        const num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    // Función para manejar cambios manuales en tergal cuando no hay largo de cortina
    function manejarTergalManual() {
        const largoCortinaInput = document.getElementById('largo');
        const largoTergalInput = document.getElementById('largo_tergal');

        // Solo permitir modificación manual si no hay largo de cortina
        if (!largoCortinaInput || !largoCortinaInput.value) {
            largoTergalInput.dataset.manual = 'true';
            actualizarLargoForro();
        }
    }

    // Inicializar valores al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos los campos
        actualizarLargoTergal();
        actualizarLargoForro();

        // Inicializar bastillas si ya tienen valores
        const bastillaInputs = ['valor_bastilla', 'valor_bastilla_tergal', 'valor_bastilla_forro'];
        bastillaInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input && input.value) {
                input.dataset.lastValue = input.value;
            }
        });
    });

    // Script para calcular No. Lienzos
    function calcularLienzos() {
        const ancho = parseFloat(document.getElementById('ancho')?.value || 0);
        const anchoTela = parseFloat(document.getElementById('ancho_tela')?.value || 0);

        if (ancho > 0 && anchoTela > 0) {
            const lienzos = (ancho * 2.5) / anchoTela;
            const lienzosRedondeado = Math.ceil(lienzos);

            document.getElementById('no_lienzos').value = lienzos.toFixed(2);
            document.getElementById('no_lienzos_redondeado').value = lienzosRedondeado;
        } else {
            document.getElementById('no_lienzos').value = '';
            document.getElementById('no_lienzos_redondeado').value = '';
        }
        document.addEventListener('input', function(e) {
            if (e.target.id === 'no_lienzos') {
                const noLienzos = parseFloat(e.target.value) || 0;
                document.getElementById('no_lienzos_redondeado').value = Math.ceil(noLienzos);
                actualizarTablaTotales && actualizarTablaTotales();
            }
        });
    }

    // Script para calcular el total de tela, tergal y forro para la tabla de totales y el costo de mano de obra
    document.addEventListener('change', function() {
        // Cortina
        const noLienzosCortina = parseFloat(document.getElementById('no_lienzos_redondeado')?.value);
        const largoCortina = parseFloat(document.getElementById('largo')?.value);
        const bastillaCortina = parseFloat(document.getElementById('valor_bastilla')?.value) || 0;
        const precioTela = parseFloat(document.getElementById('precio_m2_tela')?.value);

        // Tergal
        const noLienzosTergal = parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value);
        const largoTergal = parseFloat(document.getElementById('largo_tergal')?.value);
        const bastillaTergal = parseFloat(document.getElementById('valor_bastilla_tergal')?.value) || 0;
        const precioTergal = parseFloat(document.getElementById('precio_m2_tergal')?.value);

        // Forro
        const noLienzosForro = parseFloat(document.getElementById('no_lienzos_redondeado_forro')?.value);
        const largoForro = parseFloat(document.getElementById('largo_forro')?.value);
        const bastillaForro = parseFloat(document.getElementById('valor_bastilla_forro')?.value) || 0;
        const precioForro = parseFloat(document.getElementById('precio_m2_forro')?.value);

        // Suma la bastilla solo en el cálculo
        const totalTela = (!isNaN(noLienzosCortina) && !isNaN(largoCortina)) ? (noLienzosCortina * (largoCortina + bastillaCortina)) : 0;
        const totalTergal = (!isNaN(noLienzosTergal) && !isNaN(largoTergal)) ? (noLienzosTergal * (largoTergal + bastillaTergal)) : 0;
        const totalForro = (!isNaN(noLienzosForro) && !isNaN(largoForro)) ? (noLienzosForro * (largoForro + bastillaForro)) : 0;

        // Cálculos de totales finales
        const totalTelaFinal = (!isNaN(precioTela)) ? (totalTela * precioTela) : 0;
        const totalTergalFinal = (!isNaN(precioTergal)) ? (totalTergal * precioTergal) : 0;
        const totalForroFinal = (!isNaN(precioForro)) ? (totalForro * precioForro) : 0;

        // Actualizar campos de la tabla
        if (document.getElementById('total_tela')) document.getElementById('total_tela').value = totalTela.toFixed(2);
        if (document.getElementById('total_tergal')) document.getElementById('total_tergal').value = totalTergal.toFixed(2);
        if (document.getElementById('total_forro')) document.getElementById('total_forro').value = totalForro.toFixed(2);

        if (document.getElementById('total_tela_final')) document.getElementById('total_tela_final').value = totalTelaFinal.toFixed(2);
        if (document.getElementById('total_tergal_final')) document.getElementById('total_tergal_final').value = totalTergalFinal.toFixed(2);
        if (document.getElementById('total_final_forro')) document.getElementById('total_final_forro').value = totalForroFinal.toFixed(2);

        // Total general incluyendo forro
        if (document.getElementById('costo_total_tela_tergal_forro')) {
            document.getElementById('costo_total_tela_tergal_forro').value = (totalTelaFinal + totalTergalFinal + totalForroFinal).toFixed(2);
        }
        // Cálculo de Mano de Obra
        const m2CortinaInput = document.querySelector('[name="detalle[m2_1]"]');
        const m2TergalInput = document.querySelector('[name="detalle[m2_2]"]');

        const costoMO1 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_1]"]')?.value) || 0;
        const costoMO2 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_2]"]')?.value) || 0;

        const totalMO1 = document.querySelector('[name="detalle[total_mano_obra_1]"]');
        const totalMO2 = document.querySelector('[name="detalle[total_mano_obra_2]"]');
        const costoTotalMO = document.querySelector('[name="detalle[costo_total_mano_obra]"]');

        // Actualizar m² en campos de mano de obra
        if (m2CortinaInput) m2CortinaInput.value = totalTela.toFixed(2);
        if (m2TergalInput) m2TergalInput.value = totalTergal.toFixed(2);

        // Calcular totales de mano de obra
        const totalMano1 = totalTela * costoMO1;
        const totalMano2 = totalTergal * costoMO2;

        if (totalMO1) totalMO1.value = totalMano1.toFixed(2);
        if (totalMO2) totalMO2.value = totalMano2.toFixed(2);

        if (costoTotalMO) costoTotalMO.value = (totalMano1 + totalMano2).toFixed(2);
    });

    // Actualizar el precio de mano de obra al cambiar el ancho de la tela
    function actualizarPrecioManoObra() {
        // --- CORTINA ---
        const anchoTelaInput = document.getElementById('ancho_tela');
        const manoObraInput = document.querySelector('input[name="detalle[costo_mano_obra_1]"]');
        const m2CortinaInput = document.querySelector('[name="detalle[m2_1]"]');
        const totalMO1 = document.querySelector('[name="detalle[total_mano_obra_1]"]');

        if (anchoTelaInput && manoObraInput) {
            let ancho = parseFloat(anchoTelaInput.value) || 0;
            let anchoEnCm = ancho <= 10 ? ancho * 100 : ancho;
            const valorBaseManoObra = obtenerValorBaseManoObra();

            // Actualizar costo unitario de mano de obra cortina
            manoObraInput.value = (anchoEnCm >= 280 ? valorBaseManoObra * 2 : valorBaseManoObra).toFixed(2);

            // Recalcular total de mano de obra cortina
            const costoMO1 = parseFloat(manoObraInput.value) || 0;
            const totalTela = parseFloat(m2CortinaInput?.value) || 0;
            const totalMano1 = totalTela * costoMO1;
            if (totalMO1) totalMO1.value = totalMano1.toFixed(2);
        }

        // --- TERGAL ---
        const anchoTergalInput = document.getElementById('ancho_tergal');
        const manoObraTergalInput = document.querySelector('input[name="detalle[costo_mano_obra_2]"]');
        const m2TergalInput = document.querySelector('[name="detalle[m2_2]"]');
        const totalMO2 = document.querySelector('[name="detalle[total_mano_obra_2]"]');

        if (anchoTergalInput && manoObraTergalInput) {
            let anchoTergal = parseFloat(anchoTergalInput.value) || 0;
            let anchoTergalEnCm = anchoTergal <= 10 ? anchoTergal * 100 : anchoTergal;
            const valorBaseManoObraTergal = obtenerValorBaseManoObraTergal();

            // Actualizar costo unitario de mano de obra tergal
            manoObraTergalInput.value = (anchoTergalEnCm >= 280 ? valorBaseManoObraTergal * 2 : valorBaseManoObraTergal).toFixed(2);

            // Recalcular total de mano de obra tergal
            const costoMO2 = parseFloat(manoObraTergalInput.value) || 0;
            const totalTergal = parseFloat(m2TergalInput?.value) || 0;
            const totalMano2 = totalTergal * costoMO2;
            if (totalMO2) totalMO2.value = totalMano2.toFixed(2);
        }

        const costoTotalMO = document.querySelector('[name="detalle[costo_total_mano_obra]"]');
        if (costoTotalMO) {
            const totalMano1 = parseFloat(totalMO1?.value) || 0;
            const totalMano2 = parseFloat(totalMO2?.value) || 0;
            costoTotalMO.value = (totalMano1 + totalMano2).toFixed(2);
        }

        if (typeof actualizarTablaTotales === 'function') {
            actualizarTablaTotales();
        }
    }

    function obtenerValorBaseManoObra() {
        const valorBase = document.querySelector('#valor_base_mano_obra')?.value;
        if (valorBase) {
            return parseFloat(valorBase);
        }
        return 120;
    }

    function obtenerValorBaseManoObraTergal() {
        const valorBase = document.querySelector('#valor_base_mano_obra_tergal')?.value;
        if (valorBase) {
            return parseFloat(valorBase);
        }
        return 100;
    }

    // Ejecutar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        const anchoTelaInput = document.getElementById('ancho_tela');
        const anchoTergalInput = document.getElementById('ancho_tergal');

        if (anchoTelaInput) {
            anchoTelaInput.addEventListener('input', actualizarPrecioManoObra);
            anchoTelaInput.addEventListener('change', actualizarPrecioManoObra);
        }

        if (anchoTergalInput) {
            anchoTergalInput.addEventListener('input', actualizarPrecioManoObra);
            anchoTergalInput.addEventListener('change', actualizarPrecioManoObra);
        }

        actualizarPrecioManoObra();
    });

    let contadorOtros = 1;

    // Insumos precargados desde el backend
    const insumosDisponibles = @json($insumos); // Marca error pero funciona igual
    const cortinerosDisponibles = @json($cortineros);

    // Scripts para calcular el costo total de materiales
    function crearSelectInsumos(nombreInput) {
        const select = document.createElement('select');
        select.classList.add('form-select', 'select2');
        select.name = nombreInput;

        const defaultOption = document.createElement('option');
        defaultOption.value = "";
        defaultOption.textContent = "Seleccione un insumo";
        select.appendChild(defaultOption);

        // Insumos normales
        insumosDisponibles.forEach(insumo => {
            const option = document.createElement('option');
            option.value = insumo.id;
            option.textContent = insumo.nombre;
            option.dataset.precio = insumo.precio_publico;
            select.appendChild(option);
        });

        // Cortineros (optgroup para diferenciarlos)
        if (cortinerosDisponibles && cortinerosDisponibles.length > 0) {
            const cortineroGroup = document.createElement('optgroup');
            cortineroGroup.label = 'Cortineros';
            cortinerosDisponibles.forEach(cortinero => {
                const option = document.createElement('option');
                option.value = 'cortinero_' + cortinero.id;
                option.textContent = cortinero.nombre;
                option.dataset.precio = cortinero.precio_publico;
                cortineroGroup.appendChild(option);
            });
            select.appendChild(cortineroGroup);
        }

        return select;
    }

    function añadirOtroInsumo() {
        const tbody = document.getElementById('materiales-tbody');
        const fila = document.createElement('tr');

        // Celda de selección de insumo
        const tdNombre = document.createElement('td');
        const selectInsumo = crearSelectInsumos(`detalle[otros${contadorOtros}_nombre]`);
        tdNombre.appendChild(selectInsumo);

        // Celda cantidad
        const tdCantidad = document.createElement('td');
        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.name = `detalle[otros${contadorOtros}_cantidad]`;
        inputCantidad.classList.add('form-control');
        inputCantidad.step = 1;
        inputCantidad.min = 0;
        tdCantidad.appendChild(inputCantidad);

        // Celda precio
        const tdPrecio = document.createElement('td');
        const inputGroupPrecio = document.createElement('div');
        inputGroupPrecio.classList.add('input-group');
        const spanPrecio = document.createElement('span');
        spanPrecio.classList.add('input-group-text');
        spanPrecio.textContent = '$';
        const inputPrecio = document.createElement('input');
        inputPrecio.type = 'number';
        inputPrecio.name = `detalle[otros${contadorOtros}_precio]`;
        inputPrecio.classList.add('form-control');
        inputPrecio.step = 0.01;
        inputPrecio.min = 0;
        inputPrecio.readOnly = true;
        inputGroupPrecio.appendChild(spanPrecio);
        inputGroupPrecio.appendChild(inputPrecio);
        tdPrecio.appendChild(inputGroupPrecio);

        // Celda subtotal
        const tdSubtotal = document.createElement('td');
        const inputGroupSubtotal = document.createElement('div');
        inputGroupSubtotal.classList.add('input-group');
        const spanSubtotal = document.createElement('span');
        spanSubtotal.classList.add('input-group-text');
        spanSubtotal.textContent = '$';
        const inputSubtotal = document.createElement('input');
        inputSubtotal.type = 'number';
        inputSubtotal.classList.add('form-control');
        inputSubtotal.readOnly = true;
        inputSubtotal.step = 0.01;
        inputGroupSubtotal.appendChild(spanSubtotal);
        inputGroupSubtotal.appendChild(inputSubtotal);
        tdSubtotal.appendChild(inputGroupSubtotal);

        // Celda eliminar
        const tdEliminar = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.classList.add('btn', 'btn-danger', 'btn-sm');
        btnEliminar.innerText = 'Eliminar';
        btnEliminar.onclick = () => {
            fila.remove();
            actualizarCostoTotal();
        };
        tdEliminar.appendChild(btnEliminar);

        fila.appendChild(tdNombre);
        fila.appendChild(tdCantidad);
        fila.appendChild(tdPrecio);
        fila.appendChild(tdSubtotal);
        fila.appendChild(tdEliminar);

        tbody.appendChild(fila);
        contadorOtros++;

        // Inicializar select2 para el nuevo select
        $(selectInsumo).select2({
            dropdownParent: $('#tabla-materiales-varios')
        });

        // Actualizar el precio al seleccionar un insumo
        $(selectInsumo).on('change', function() {
            const selected = $(this).find('option:selected');
            const precio = selected.data('precio');
            if (precio !== undefined && precio !== null && precio !== '') {
                inputPrecio.value = precio;
            } else {
                inputPrecio.value = '';
            }
            calcularSubtotal();
            actualizarCostoTotal();
        });

        // Calcular subtotal cuando cambie cantidad o precio
        function calcularSubtotal() {
            const cantidad = parseFloat(inputCantidad.value) || 0;
            const precio = parseFloat(inputPrecio.value) || 0;
            inputSubtotal.value = (cantidad * precio).toFixed(2);
        }

        inputCantidad.addEventListener('input', function() {
            calcularSubtotal();
            actualizarCostoTotal();
        });
        inputPrecio.addEventListener('input', function() {
            calcularSubtotal();
            actualizarCostoTotal();
        });
    }

    function actualizarCostoTotal() {
        const tbody = document.getElementById('materiales-tbody');
        let total = 0;

        Array.from(tbody.querySelectorAll('tr')).forEach(fila => {
            // Busca el input de cantidad
            const cantidadInput = fila.querySelector('input[name*="_cantidad"]');
            // Busca el input de precio (puede ser readonly o editable)
            let precioInput = fila.querySelector('input[type="number"].form-control[readonly]');
            if (!precioInput) {
                // Si no es readonly, busca el editable (para insumos "otros")
                precioInput = fila.querySelector('input[name*="_precio"]');
            }

            const cantidad = parseFloat(cantidadInput?.value) || 0;
            const precio = parseFloat(precioInput?.value) || 0;

            total += cantidad * precio;
        });

        document.getElementById('costo_total_materiales').value = total.toFixed(2);
        actualizarTablaTotales();
    }

    function actualizarTablaTotales() {
        // Totales de tela, tergal y forro
        const totalTelaFinal = parseFloat(document.getElementById('total_tela_final')?.value) || 0;
        const totalTergalFinal = parseFloat(document.getElementById('total_tergal_final')?.value) || 0;
        const totalForroFinal = parseFloat(document.getElementById('total_final_forro')?.value) || 0;
        const costoManoObra = parseFloat(document.querySelector('[name="detalle[costo_total_mano_obra]"]')?.value) || 0;
        const costoMateriales = parseFloat(document.getElementById('costo_total_materiales')?.value) || 0;

        // Suma todos los costos
        const costoCortina = totalTelaFinal + totalTergalFinal + totalForroFinal + costoManoObra + costoMateriales;
        document.getElementById('costo_cortina').value = costoCortina > 0 ? costoCortina.toFixed(2) : '';

        // Utilidad
        const utilidad = costoCortina * 0.15;
        document.querySelectorAll('input[name="totales[utilidad]"]').forEach(function(input) {
            input.value = utilidad > 0 ? utilidad.toFixed(2) : '';
        });

        // Costo decorador
        const decoradorPorcentajeInput = document.getElementById('decorador_porcentaje');
        const decoradorPorcentaje = decoradorPorcentajeInput ? (parseFloat(decoradorPorcentajeInput.value) || 0) : 15;
        const costoDecorador = costoCortina + (costoCortina * (decoradorPorcentaje / 100));
        if (document.getElementById('costo_decorador')) {
            document.getElementById('costo_decorador').value = costoDecorador > 0 ? costoDecorador.toFixed(2) : '';
        }

        // Precio público
        let precioPublico = costoCortina * 2;

        // Descuento
        const descuentoInput = document.getElementById('descuento');
        const descuento = descuentoInput ? (parseFloat(descuentoInput.value) || 0) : 0;
        if (descuento > 0) {
            precioPublico = precioPublico - (precioPublico * (descuento / 100));
        }

        // IVA
        const aplicarIVA = document.getElementById('aplicar_iva')?.checked;
        if (aplicarIVA) {
            precioPublico = precioPublico * 1.16;
        }

        document.getElementById('precio_publico').value = precioPublico > 0 ? precioPublico.toFixed(2) : '';

        // Total Lienzos
        const totalLienzos =
            (parseFloat(document.getElementById('no_lienzos_redondeado')?.value) || 0) +
            (parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value) || 0) +
            (parseFloat(document.getElementById('no_lienzos_redondeado_forro')?.value) || 0);
        document.getElementById('total_lienzos').value = totalLienzos > 0 ? totalLienzos : '';

        // Total m2 Tela
        const totalM2Tela = parseFloat(document.getElementById('total_tela')?.value) || 0;
        document.getElementById('total_m2_tela').value = totalM2Tela > 0 ? totalM2Tela : '';

        // Total m2 Tergal
        const totalM2Tergal = parseFloat(document.getElementById('total_tergal')?.value) || 0;
        document.getElementById('total_m2_tergal').value = totalM2Tergal > 0 ? totalM2Tergal : '';

        // Total m2 Forro
        const totalM2Forro = parseFloat(document.getElementById('total_forro')?.value) || 0;
        document.getElementById('total_m2_forro').value = totalM2Forro > 0 ? totalM2Forro : '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('aplicar_iva')?.addEventListener('change', actualizarTablaTotales);
        document.getElementById('descuento')?.addEventListener('input', actualizarTablaTotales);
    });

    // Escuchar cambios en los campos de lienzos redondeados, total_forro, total_tela y total_tergal
    document.addEventListener('input', function(e) {
        if (
            e.target.id === 'no_lienzos_redondeado' ||
            e.target.id === 'no_lienzos_redondeado_tergal' ||
            e.target.id === 'total_forro' ||
            e.target.id === 'total_tela' ||
            e.target.id === 'total_tergal'
        ) {
            actualizarTablaTotales();
        }
    });

    document.addEventListener('input', function(e) {
        if (['ancho', 'ancho_tela'].includes(e.target.id)) {
            calcularLienzos();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const cortina = document.getElementById('cortinaCheck');
        const tergal = document.getElementById('tergalCheck');
        const forro = document.getElementById('forroCheck');

        const tablas = [
            document.getElementById('tabla-totales-tela-tergal'),
            document.getElementById('tabla-mano-obra'),
            document.getElementById('tabla-materiales-varios'),
            document.getElementById('tabla-totales')
        ];

        function mostrarOcultarTablas() {
            if (cortina.checked || tergal.checked || forro.checked) {
                tablas.forEach(tabla => tabla && tabla.classList.remove('d-none'));
            } else {
                tablas.forEach(tabla => tabla && tabla.classList.add('d-none'));
            }
        }

        cortina.addEventListener('change', mostrarOcultarTablas);
        tergal.addEventListener('change', mostrarOcultarTablas);
        forro.addEventListener('change', mostrarOcultarTablas);

        // Oculta al cargar la página
        mostrarOcultarTablas();
    });

    document.addEventListener('change', function(e) {
        if (
            e.target.id === 'no_lienzos_redondeado' ||
            e.target.id === 'largo' ||
            e.target.id === 'precio_m2_tela' ||
            e.target.id === 'total_tela'
        ) {
            actualizarTablaTotales();
        }
    });

    $(document).on('change', '#tela_id', function() {
        const precio = $(this).find('option:selected').data('precio');
        $('#precio_m2_tela').val(Number(precio).toFixed(2));

        // Recalcular total_tela (m²) al cambiar la tela
        const noLienzosCortina = parseFloat($('#no_lienzos_redondeado').val()) || 0;
        const largoCortina = parseFloat($('#largo').val()) || 0;
        const bastillaCortina = parseFloat($('#valor_bastilla').val()) || 0;
        const totalTela = noLienzosCortina * (largoCortina + bastillaCortina);
        $('#total_tela').val(totalTela.toFixed(2));

        // Calcular total final de tela
        const total = totalTela * Number(precio);
        $('#total_tela_final').val(total.toFixed(2));

        // Actualizar costo total combinado
        const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
        const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
        $('#costo_total_tela_tergal_forro').val((total + totalTergalFinal + totalForroFinal).toFixed(2));

        actualizarTablaTotales();
    });

    $(document).on('change', '#tergal_id', function() {
        const precio = $(this).find('option:selected').data('precio');
        $('#precio_m2_tergal').val(Number(precio).toFixed(2));

        const metros = parseFloat($('#total_tergal').val()) || 0;
        const total = metros * Number(precio);
        $('#total_tergal_final').val(total.toFixed(2));

        const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
        const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
        $('#costo_total_tela_tergal_forro').val((totalTelaFinal + total + totalForroFinal).toFixed(2));


        actualizarTablaTotales();
    });

    $(document).on('change', '#forro_id', function() {
        const precio = $(this).find('option:selected').data('precio');
        $('#precio_m2_forro').val(Number(precio).toFixed(2));

        const metros = parseFloat($('#total_forro').val()) || 0;
        const total = metros * Number(precio);
        $('#total_final_forro').val(total.toFixed(2));

        const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
        const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
        $('#costo_total_tela_tergal_forro').val((totalTelaFinal + totalTergalFinal + total).toFixed(2));

        actualizarTablaTotales();
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'precio_m2_tela') {
            const metros = parseFloat($('#total_tela').val()) || 0;
            const precio = parseFloat($('#precio_m2_tela').val()) || 0;
            const total = metros * precio;
            $('#total_tela_final').val(total.toFixed(2));

            const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
            const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
            $('#costo_total_tela_tergal_forro').val((total + totalTergalFinal + totalForroFinal).toFixed(2));

            actualizarTablaTotales();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'precio_m2_tergal') {
            const metros = parseFloat($('#total_tergal').val()) || 0;
            const precio = parseFloat($('#precio_m2_tergal').val()) || 0;
            const total = metros * precio;
            $('#total_tergal_final').val(total.toFixed(2));

            const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
            const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
            $('#costo_total_tela_tergal_forro').val((totalTelaFinal + total + totalForroFinal).toFixed(2));

            actualizarTablaTotales();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.id === 'precio_m2_forro') {
            const metros = parseFloat($('#total_forro').val()) || 0;
            const precio = parseFloat($('#precio_m2_forro').val()) || 0;
            const total = metros * precio;
            $('#total_final_forro').val(total.toFixed(2));

            const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
            const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
            $('#costo_total_tela_tergal_forro').val((totalTelaFinal + totalTergalFinal + total).toFixed(2));

            actualizarTablaTotales();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'total_tela') {
            const metros = parseFloat($('#total_tela').val()) || 0;
            const precio = parseFloat($('#precio_m2_tela').val()) || 0;
            const total = metros * precio;
            $('#total_tela_final').val(total.toFixed(2));

            const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
            const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
            $('#costo_total_tela_tergal_forro').val((total + totalTergalFinal + totalForroFinal).toFixed(2));

            actualizarTablaTotales();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'total_tergal') {
            const metros = parseFloat($('#total_tergal').val()) || 0;
            const precio = parseFloat($('#precio_m2_tergal').val()) || 0;
            const total = metros * precio;
            $('#total_tergal_final').val(total.toFixed(2));

            const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
            const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
            $('#costo_total_tela_tergal_forro').val((totalTelaFinal + total + totalForroFinal).toFixed(2));

            actualizarTablaTotales();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'total_forro') {
            const metros = parseFloat($('#total_forro').val()) || 0;
            const precio = parseFloat($('#precio_m2_forro').val()) || 0;
            const total = metros * precio;
            $('#total_final_forro').val(total.toFixed(2));
            $('#costo_total_forro').val(total.toFixed(2));

            const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
            const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;

            $('#costo_total_tela_tergal_forro').val((totalTelaFinal + totalTergalFinal + total).toFixed(2));

            actualizarTablaTotales();
        }
    });

    // Event listeners globales
    document.addEventListener('input', function(e) {
        // Recalcula cuando cambian campos relevantes
        if (
            e.target.id === 'total_tela_final' ||
            e.target.id === 'total_tergal_final' ||
            e.target.id === 'total_final_forro' ||
            e.target.name === 'detalle[costo_total_mano_obra]' ||
            e.target.id === 'costo_total_materiales' ||
            e.target.name === 'totales[decorador_porcentaje]' ||
            e.target.id === 'decorador_porcentaje' ||
            e.target.id === 'descuento' ||
            e.target.id === 'aplicar_iva'
        ) {
            console.log('Event listener activado por:', e.target.id || e.target.name);
            actualizarTablaTotales();
        }
    });

    document.addEventListener('change', function(e) {
        if (
            e.target.id === 'total_tela_final' ||
            e.target.id === 'total_tergal_final' ||
            e.target.id === 'total_final_forro' ||
            e.target.name === 'detalle[costo_total_mano_obra]' ||
            e.target.id === 'costo_total_materiales' ||
            e.target.name === 'totales[decorador_porcentaje]' ||
            e.target.id === 'decorador_porcentaje' ||
            e.target.id === 'descuento' ||
            e.target.id === 'aplicar_iva'
        ) {
            console.log('Change event activado por:', e.target.id || e.target.name);
            actualizarTablaTotales();
        }
    });


</script>

<script>
    // Bloquea el envío del formulario al presionar Enter en los inputs
document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter' && event.target.tagName === 'INPUT') {
        event.preventDefault();
    }
});
</script>
@endsection
