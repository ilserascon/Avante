@extends('layouts.stisla')

@section('title', 'Editar Cotización')

@section('content')
@php
    $limpiarPrecio = function ($valor) {
        $valor = str_replace(['$', ' '], '', (string) $valor);
        $valor = str_replace(',', '.', $valor);

        return floatval($valor);
    };
@endphp
<style>
    .cotinero-row-label {
        font-size: 1rem;
        font-weight: 600;
        color: #34395e;
        line-height: 1.3;
        margin-top: 0.5rem;
    }

    .materiales-varios-table {
        table-layout: fixed;
        width: 100%;
    }

    .materiales-varios-table th:first-child,
    .materiales-varios-table td:first-child {
        width: 40%;
        max-width: 40%;
    }

    .materiales-varios-table .select2-container {
        width: 100% !important;
        max-width: 100%;
    }

    .materiales-varios-table .select2-container .select2-selection--single .select2-selection__rendered {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-right: 24px;
    }

    .cotizacion-type-options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .cotizacion-type-card {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.85rem 1rem;
        border: 1px solid #dfe4ea;
        border-radius: 0.85rem;
        background: #fff;
        min-width: 150px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .cotizacion-type-card:has(.form-check-input:checked) {
        border-color: #6777ef;
        background: #f5f7ff;
        box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.12);
    }

    .cotizacion-type-card .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0;
        flex-shrink: 0;
    }

    .cotizacion-edit .status-chip {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .cotizacion-edit .status-solicitada { color: #8b5e00; background: #fff4d9; }
    .cotizacion-edit .status-aceptada { color: #166534; background: #dcfce7; }
    .cotizacion-edit .status-rechazada { color: #991b1b; background: #fee2e2; }
    .cotizacion-edit .status-completada { color: #1e40af; background: #dbeafe; }
    .cotizacion-edit .status-cancelada { color: #9a3412; background: #ffedd5; }

    .cotizacion-type-card .form-check-label {
        margin-bottom: 0;
        font-weight: 600;
        cursor: pointer;
    }

    .productos-table .producto-fila > td {
        border-top: 0;
        padding: 0;
    }

    .productos-table .producto-fila-card {
        background: #f8f9fb;
        border-color: #e4e8ee !important;
    }

    .productos-table .producto-fila-card .form-control {
        min-height: 38px;
    }

    .productos-table .producto-precio,
    .productos-table .producto-subtotal {
        text-align: right;
        font-weight: 600;
    }

    .productos-table .select2-container {
        width: 100% !important;
    }

    .insumos-table .insumo-fila > td {
        border-top: 0;
        padding: 0;
    }

    .insumos-table .insumo-fila-card {
        background: #f8f9fb;
        border-color: #e4e8ee !important;
    }

    .insumos-table .insumo-fila-card .form-control {
        min-height: 38px;
    }

    .insumos-table .insumo-precio,
    .insumos-table .insumo-subtotal {
        text-align: right;
        font-weight: 600;
    }

    .insumos-table .select2-container {
        width: 100% !important;
    }

    .select2-container--open {
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
    }

    .select2-container .select2-selection--single {
        min-height: 38px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
</style>
<div class="section">
    <div class="section-header bg-white border rounded shadow-sm px-4 py-3 d-block">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4">
            <div>
                <h1 class="mb-1">Editar Cotización</h1>
            </div>
            <div class="mt-3 mt-lg-0">
                <button type="button" class="btn btn-success px-4" id="agregar-cotizacion-btn">
                    <i class="fas fa-plus mr-1"></i> Agregar Cortina/Tergal
                </button>
            </div>
        </div>

        <div class="row align-items-end">
            <div class="col-lg-7 col-md-6 mb-3 mb-md-0">
                <label for="cliente_id" class="font-weight-semibold mb-2">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-control select2" required autocomplete="off" form="cotizacion-form">
                    <option value="">Seleccione un cliente</option>
                    @foreach(\App\Models\Cliente::where('borrado', 0)->orderBy('nombre')->get() as $cliente)
                    <option value="{{ $cliente->id }}" {{ (int) old('cliente_id', $cotizacion->cliente_id) === (int) $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-3 mb-3 mb-md-0">
                <label for="fecha" class="font-weight-semibold mb-2">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ old('fecha', $cotizacion->fecha) }}" form="cotizacion-form">
            </div>
            <div class="col-lg-2 col-md-3">
                @php
                    $estatusActual = $cotizacion->estatus;
                    $estatusClass = in_array(strtolower((string) $estatusActual), ['solicitada', 'aceptada', 'rechazada', 'completada', 'cancelada'], true)
                        ? 'status-' . strtolower((string) $estatusActual)
                        : 'status-solicitada';
                @endphp
                <label class="font-weight-semibold mb-2 d-block">Estatus</label>
                <div class="cotizacion-edit">
                    <span class="status-chip {{ $estatusClass }}">{{ ucfirst($estatusActual) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section-body">
        <form id="cotizacion-form" method="POST" action="{{ route('admin.cotizaciones.update', $cotizacion->id) }}">
            @csrf
            @method('PUT')
            @if (session('error'))
                <div class="alert alert-danger">{!! session('error') !!}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">{!! session('warning') !!}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card mb-3">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="cotizacion-tabs" role="tablist"></ul>
                    <div class="tab-content border border-top-0 p-3" id="cotizacion-tabs-content"></div>
                </div>
            </div>


            <!-- Tabla Totales Tela, Tergal y Forro -->
            <div class="card mt-4 border-0 shadow-sm d-none" id="tabla-totales-tela-tergal">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h4 class="mb-1">Totales Tela, Tergal y Forro</h4>
                    <div class="text-muted small">Concentrado de metros cuadrados, precios y total por material textil.</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Descripción</th>
                                    <th>M²</th>
                                    <th>Precio m²</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Fila Cortina -->
                                <tr>
                                    <td> Cortina </td>
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
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_tela_final]" id="total_tela_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_tela_final', $detalleCotizacion->total_tela_final ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Tergal -->
                                <tr>
                                    <td> Tergal </td>
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
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_tergal_final]" id="total_tergal_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_tergal_final', $detalleCotizacion->total_tergal_final ?? '') }}">
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Forro -->
                                <tr>
                                    <td> Forro </td>
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
            <div class="card mt-4 d-none border-0 shadow-sm" id="tabla-mano-obra">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h4 class="mb-1">Mano de Obra</h4>
                    <div class="text-muted small">Resumen de costos operativos vinculados a cortina y tergal.</div>
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
                                                value="{{ old('detalle.costo_mano_obra_1', $detalleCotizacion->costo_mano_obra_1 ?? ($manoObra['Mano de Obra Cortina']->precio_publico ?? '')) }}">
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
                                                value="{{ old('detalle.costo_mano_obra_2', $detalleCotizacion->costo_mano_obra_2 ?? ($manoObra['Mano de Obra Tergal']->precio_publico ?? '')) }}">
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
            @php $vePreciosMaterialesVarios = auth()->user()?->veCostosCotizacion() ?? false; @endphp
            <div class="card mt-4 d-none border-0 shadow-sm" id="tabla-materiales-varios">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h4 class="mb-1">Materiales Varios</h4>
                    <div class="text-muted small">Accesorios, cortineros y materiales complementarios para la instalacion.</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle materiales-varios-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40%;">Materiales Varios</th>
                                    <th style="min-width: 120px;">Cantidad</th>
                                    <th style="min-width: 150px;" @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>Precio Unitario</th>
                                    <th style="min-width: 150px;" @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="materiales-tbody">
                                <!-- Insumos fijos -->
                                <tr>
                                    <td>
                                        <div class="cotinero-row-label mb-2">Cortinero cortina</div>
                                        <select name="detalle[cortinero_id]" id="cortinero_id" class="form-select select2 w-100">
                                            <option value="">Seleccione tipo de cortinero</option>
                                            @foreach($cortineros as $cortinero)
                                            <option value="{{ $cortinero->id }}" data-precio="{{ $cortinero->precio }}">
                                                {{ $cortinero->etiquetaCortinero() }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[cortinero_cantidad]" step="1" id="cortinero_cantidad" class="form-control" value="1" readonly>
                                    </td>
                                    <td @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_precio" name="detalle[cortinero_precio]" class="form-control" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_subtotal" class="form-control" readonly step="0.01">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="cotinero-row-label mb-2">Cortinero tergal</div>
                                        <select name="detalle[cortinero_tergal_id]" id="cortinero_tergal_id" class="form-select select2 w-100">
                                            <option value="">Seleccione tipo de cortinero</option>
                                            @foreach($cortineros as $cortinero)
                                            <option value="{{ $cortinero->id }}" data-precio="{{ $cortinero->precio }}">
                                                {{ $cortinero->etiquetaCortinero() }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[cortinero_tergal_cantidad]" step="1" id="cortinero_tergal_cantidad" class="form-control" value="1" readonly>
                                    </td>
                                    <td @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_tergal_precio" name="detalle[cortinero_tergal_precio]" class="form-control" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_tergal_subtotal" class="form-control" readonly step="0.01">
                                        </div>
                                    </td>
                                </tr>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Cortinero cortina
                                        reinicializarSelect2('#cortinero_id');
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
                                        reinicializarSelect2('#cortinero_tergal_id');
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
                                    <td colspan="3" class="text-start">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="añadirOtroInsumo()"><i class="fas fa-plus"></i> Añadir otro</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="{{ $vePreciosMaterialesVarios ? 3 : 2 }}" class="text-end"><strong>Costo Total Materiales:</strong></td>
                                    <td @class(['' => $vePreciosMaterialesVarios, 'd-none' => !$vePreciosMaterialesVarios])>
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
            @php
                $veUtilidad = auth()->user()?->veUtilidadCotizacion() ?? false;
                $veCostos = auth()->user()?->veCostosCotizacion() ?? false;
            @endphp
            <div class="card mt-4 border-0 shadow-sm d-none" id="tabla-totales">
                <div class="card-header bg-light border-bottom-0 py-3">
                    <h4 class="mb-1">Totales</h4>
                    <div class="text-muted small">Resultado economico final con decorador, descuento e IVA.</div>
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
                                    @if($veCostos)
                                    <tr>
                                        <td><strong>Costo Cortina</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="costo_cortina" name="totales[costo_cortina]" value="{{ old('totales.costo_cortina', $cotizacion->costo_cortina ?? '') }}" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    @else
                                        <input type="hidden" id="costo_cortina" name="totales[costo_cortina]" value="{{ old('totales.costo_cortina', $cotizacion->costo_cortina ?? '') }}">
                                    @endif
                                    @if($veUtilidad)
                                    <tr>
                                        <td><strong>Utilidad</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="utilidad" name="totales[utilidad]" value="{{ old('totales.utilidad', $cotizacion->utilidad ?? '') }}" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    @else
                                        <input type="hidden" id="utilidad" name="totales[utilidad]" value="{{ old('totales.utilidad', $cotizacion->utilidad ?? '') }}">
                                    @endif
                                    @if($veCostos)
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
                                        <input type="hidden" id="decorador_porcentaje" name="totales[decorador_porcentaje]" value="{{ old('totales.decorador_porcentaje', $detalleCotizacion->decorador_porcentaje ?? 15) }}">
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
                                                    <label for="descuento" class="form-label mb-0">Descuento (%)</label>
                                                    <input type="number" class="form-control" id="descuento" name="descuento" min="0" max="100" step="0.01" value="{{ old('descuento', $cotizacion->descuento ?? 0) }}">
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

            <div class="bg-white border rounded shadow-sm px-4 py-3 mt-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <div class="font-weight-bold">Acciones de la cotización</div>
                        <div class="text-muted small">Revisa la información capturada antes de guardar o cancelar el borrador.</div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row">
                        <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-light border mr-sm-2 mb-2 mb-sm-0">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">Actualizar Cotización</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<select id="plantilla_tela" class="d-none">
    <option value="">Seleccione una tela</option>
    @foreach($telas as $tela)
    @php
    if($limpiarPrecio($tela->precio_publico) > 0) { // Prioriza precio_publico si es un número válido y mayor a 0
    $precio = $limpiarPrecio($tela->precio_publico);
    } elseif($limpiarPrecio($tela->campo6) > 0) { // Si campo6 es un número válido y mayor a 0, úsalo como precio
    $precio = $limpiarPrecio($tela->campo6);
    } elseif($limpiarPrecio($tela->campo13) > 0) { // Si campo13 es un número válido y mayor a 0, úsalo como precio
    $precio = $limpiarPrecio($tela->campo13);
    } else {
    $precio = 100;
    }
    @endphp
    <option value="{{ $tela->id }}" data-precio="{{ $precio }}" data-campo1="{{ $tela->campo1Mostrar() }}">
        {{ $tela->etiquetaMaterialTextil() }}
    </option>
    @endforeach
</select>

<select id="plantilla_tergal" class="d-none">
    <option value="">Seleccione un tergal</option>
    @foreach($tergales as $tergal)
        <option
            value="{{ $tergal->id }}"
            data-precio="{{ is_numeric($tergal->precio_publico) ? $tergal->precio_publico : 0 }}"
            data-campo1="{{ $tergal->campo1Mostrar() }}"
            data-campo2="{{ $tergal->campo2Mostrar() }}"
        >
            {{ $tergal->etiquetaMaterialTextil() }}
        </option>
    @endforeach
</select>


<select id="plantilla_forro" class="d-none">
    <option value="">Seleccione un forro</option>
    @foreach($forros as $forro)
    <option
        value="{{ $forro->id }}"
        data-precio="{{ is_numeric($forro->precio_publico) ? $forro->precio_publico : 0 }}"
        data-campo1="{{ $forro->campo1Mostrar() }}"
        data-campo2="{{ $forro->campo2Mostrar() }}">
        {{ $forro->etiquetaMaterialTextil() }}
    </option>
    @endforeach
</select>

<script src="https://cdn.jsdelivr.net/npm/sweetalert@2.1.2/dist/sweetalert.min.js"></script>
<script>
    const verDetalleTelaManoObra = @json(auth()->user()->esAdministrador());
    const veUtilidadCotizacion = @json(auth()->user()->veUtilidadCotizacion());
    const veCostosCotizacion = @json(auth()->user()->veCostosCotizacion());
    const vePreciosMaterialesVarios = veCostosCotizacion;
    const claseOcultarPrecioMateriales = vePreciosMaterialesVarios ? '' : 'd-none';

    function obtenerDropdownParentSelect2() {
        return $(document.body);
    }

    function configuracionSelect2(dropdownParentOverride) {
        return {
            width: '100%',
            dropdownParent: dropdownParentOverride ? $(dropdownParentOverride) : obtenerDropdownParentSelect2(),
            dropdownAutoWidth: false,
        };
    }

    function reinicializarSelect2(select, dropdownParentOverride) {
        const $select = $(select);
        if (!$select.length) {
            return;
        }

        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2(configuracionSelect2(dropdownParentOverride));
    }

    function inicializarSelect2EnContenedor(contenedor) {
        $(contenedor).find('select.select2').each(function() {
            reinicializarSelect2(this);
        });
    }

    function inicializarSelect2MaterialesVarios(select, dropdownParent) {
        reinicializarSelect2(select, dropdownParent || document.body);
    }

    function cerrarSelect2Abiertos(exceptSelect) {
        $('select.select2').each(function() {
            if (exceptSelect && this === exceptSelect) {
                return;
            }

            if ($(this).data('select2')) {
                $(this).select2('close');
            }
        });
    }

    function etiquetaCortinero(cortinero) {
        if (!cortinero) {
            return '';
        }

        const partes = [cortinero.nombre, cortinero.descripcion, cortinero.campo1, cortinero.color]
            .map(valor => valor != null ? String(valor).trim() : '')
            .filter(valor => valor && valor.toLowerCase() !== 'null');

        return partes.join(' - ');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tabsNav = document.getElementById('cotizacion-tabs');
        const tabsContent = document.getElementById('cotizacion-tabs-content');
        const productosDisponibles = @json($productosDisponibles ?? []);
        const insumosDisponibles = @json($insumos ?? []);
        const tiposInsumoDisponibles = @json($tiposInsumoCotizacion ?? []);
        const tiposProductoDisponibles = @json($tiposProductoCotizacion ?? []);
        const cortinerosTabDisponibles = @json($cortineros ?? []);
        const insumosMaterialesVarios = @json($insumosMaterialesVarios ?? []);
        const detallesExistentes = @json($detallesExistentes ?? []);
        const insumosExistentes = @json($insumosExistentes ?? []);
        const productosExistentes = @json($productosExistentes ?? []);
        const manoObraBase = {
            cortina: {{ $manoObra['Mano de Obra Cortina']->precio_publico ?? 120 }},
            tergal: {{ $manoObra['Mano de Obra Tergal']->precio_publico ?? 100 }}
        };
        let detalleIndex = 0;
        const aplicarIvaInicial = @json((bool) old('aplicar_iva', $cotizacion->aplicar_iva ?? false));

        function etiquetaClaveNombre(item) {
            if (!item) {
                return '';
            }

            if (item.etiqueta) {
                return item.etiqueta;
            }

            const partes = [];
            const clave = item.clave != null ? String(item.clave).trim() : '';
            const nombre = item.nombre != null ? String(item.nombre).trim() : '';

            if (clave && clave.toLowerCase() !== 'null') {
                partes.push(clave);
            }

            if (nombre && nombre.toLowerCase() !== 'null') {
                partes.push(nombre);
            }

            return partes.join(' - ') || nombre || clave || '';
        }

        function obtenerOpcionesTipoInsumo(selectedId = '') {
            const opciones = tiposInsumoDisponibles.map(tipo => {
                const selected = String(tipo.id) === String(selectedId) ? 'selected' : '';
                return `<option value="${tipo.id}" ${selected}>${tipo.nombre}</option>`;
            }).join('');

            return `<option value="">Seleccione un tipo</option>${opciones}`;
        }

        function obtenerOpcionesInsumo(tipoId = '', selectedId = '') {
            if (!tipoId) {
                return `<option value="">Seleccione un insumo</option>`;
            }

            const opciones = insumosDisponibles
                .filter(insumo => String(insumo.id_tipo_insumo) === String(tipoId))
                .map(insumo => {
                    const selected = String(insumo.id) === String(selectedId) ? 'selected' : '';
                    return `<option value="${insumo.id}" data-precio="${insumo.precio_publico ?? ''}" ${selected}>${etiquetaClaveNombre(insumo)}</option>`;
                })
                .join('');

            return `<option value="">Seleccione un insumo</option>${opciones}`;
        }

        function crearFilaInsumo(index, tipoId = '', insumoId = '', cantidad = '', precio = '', descuento = '0') {
            return `
                <tr class="insumo-fila">
                    <td colspan="7" class="p-0 pb-3">
                        <div class="insumo-fila-card border rounded p-3">
                            <div class="row align-items-end g-2 mb-3">
                                <div class="col-lg-3 col-md-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Tipo de insumo</label>
                                    <select name="insumos[${index}][tipo_id]" class="form-control insumo-tipo-select">
                                        ${obtenerOpcionesTipoInsumo(tipoId)}
                                    </select>
                                </div>
                                <div class="col-lg-7 col-md-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Insumo</label>
                                    <select name="insumos[${index}][id]" class="form-control insumo-select select2">
                                        ${obtenerOpcionesInsumo(tipoId, insumoId)}
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-12 text-lg-right">
                                    <button type="button" class="btn btn-sm btn-danger remove-row">
                                        <i class="fas fa-trash-alt mr-1"></i> Quitar
                                    </button>
                                </div>
                            </div>
                            <div class="row align-items-end g-2">
                                <div class="col-xl-2 col-md-3 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Cantidad</label>
                                    <input type="number" name="insumos[${index}][cantidad]" class="form-control" step="0.01" value="${cantidad}">
                                </div>
                                <div class="col-xl-3 col-md-3 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="insumos[${index}][precio]" class="form-control insumo-precio" step="0.01" value="${precio}" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-md-2 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Desc. %</label>
                                    <input type="number" name="insumos[${index}][descuento]" class="form-control insumo-descuento" min="0" max="100" step="0.01" value="${descuento}">
                                </div>
                                <div class="col-xl-3 col-md-4 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Subtotal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="insumos[${index}][subtotal]" class="form-control insumo-subtotal" step="0.01" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }

        function obtenerOpcionesTipoProducto(selectedId = '') {
            const opciones = tiposProductoDisponibles.map(tipo => {
                const selected = String(tipo.id) === String(selectedId) ? 'selected' : '';
                return `<option value="${tipo.id}" ${selected}>${tipo.nombre}</option>`;
            }).join('');

            return `<option value="">Seleccione un tipo</option>${opciones}`;
        }

        function obtenerOpcionesProducto(tipoId = '', selectedId = '') {
            if (!tipoId) {
                return `<option value="">Seleccione un producto</option>`;
            }

            const opciones = productosDisponibles
                .filter(producto => String(producto.id_tipo_producto) === String(tipoId))
                .map(producto => {
                    const selected = String(producto.id) === String(selectedId) ? 'selected' : '';
                    return `<option value="${producto.id}" data-precio="${producto.precio_publico ?? producto.precio ?? ''}" ${selected}>${etiquetaClaveNombre(producto)}</option>`;
                })
                .join('');

            return `<option value="">Seleccione un producto</option>${opciones}`;
        }

        function crearFilaProducto(index, tipoId = '', productoId = '', cantidad = '', precio = '', descuento = '0') {
            return `
                <tr class="producto-fila">
                    <td colspan="8" class="p-0 pb-3">
                        <div class="producto-fila-card border rounded p-3">
                            <div class="row align-items-end g-2 mb-3">
                                <div class="col-lg-3 col-md-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Tipo de producto</label>
                                    <select name="productos[${index}][tipo_id]" class="form-control producto-tipo-select">
                                        ${obtenerOpcionesTipoProducto(tipoId)}
                                    </select>
                                </div>
                                <div class="col-lg-7 col-md-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Producto</label>
                                    <select name="productos[${index}][id]" class="form-control producto-select select2">
                                        ${obtenerOpcionesProducto(tipoId, productoId)}
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-12 text-lg-right">
                                    <button type="button" class="btn btn-sm btn-danger remove-row">
                                        <i class="fas fa-trash-alt mr-1"></i> Quitar
                                    </button>
                                </div>
                            </div>
                            <div class="row align-items-end g-2">
                                <div class="col-xl-2 col-md-3 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Cantidad</label>
                                    <input type="number" name="productos[${index}][cantidad]" class="form-control" step="0.01" value="${cantidad}">
                                </div>
                                <div class="col-xl-3 col-md-3 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="productos[${index}][precio]" class="form-control producto-precio" step="0.01" value="${precio}" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-md-2 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Desc. %</label>
                                    <input type="number" name="productos[${index}][descuento]" class="form-control producto-descuento" min="0" max="100" step="0.01" value="${descuento}">
                                </div>
                                <div class="col-xl-3 col-md-4 col-6">
                                    <label class="small font-weight-bold text-muted mb-1 d-block">Subtotal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="productos[${index}][subtotal]" class="form-control producto-subtotal" step="0.01" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }

        function actualizarSubtotalFila(fila, prefijo) {
            const cantidad = parseFloat(fila?.querySelector('input[name*="[cantidad]"]')?.value) || 0;
            const precio = parseFloat(fila?.querySelector(`.${prefijo}-precio`)?.value) || 0;
            const descuento = parseFloat(fila?.querySelector(`.${prefijo}-descuento`)?.value) || 0;
            const subtotalInput = fila?.querySelector(`.${prefijo}-subtotal`);
            let subtotal = cantidad * precio;

            if (descuento > 0) {
                subtotal -= subtotal * (descuento / 100);
            }

            if (subtotalInput) {
                subtotalInput.value = subtotal.toFixed(2);
            }

            actualizarTotalesConceptos();
        }

        function actualizarSelectDependiente(select, opcionesHtml) {
            select.innerHTML = opcionesHtml;
            reinicializarSelect2(select);
            $(select).trigger('change.select2');
        }

        function sumarSubtotales(selector) {
            return Array.from(tabsContent.querySelectorAll(selector)).reduce((total, input) => {
                return total + (parseFloat(input.value) || 0);
            }, 0);
        }

        function actualizarTotalesConceptos() {
            const totalInsumos = sumarSubtotales('.insumo-subtotal');
            const totalProductos = sumarSubtotales('.producto-subtotal');
            const totalPrecioPublicoPestanas = getDetallePanes().reduce((total, pane) => {
                return total + (parseFloat(pane.querySelector('.detalle-precio-publico')?.value) || 0);
            }, 0);
            let totalGeneral = totalInsumos + totalProductos + totalPrecioPublicoPestanas;

            if (document.getElementById('aplicar_iva')?.checked) {
                totalGeneral *= 1.16;
            }

            const totalInsumosTab = document.getElementById('total-insumos-tab');
            const totalProductosTab = document.getElementById('total-productos-tab');
            const totalConceptosGeneral = document.getElementById('total-conceptos-general');

            if (totalInsumosTab) totalInsumosTab.value = totalInsumos.toFixed(2);
            if (totalProductosTab) totalProductosTab.value = totalProductos.toFixed(2);
            if (totalConceptosGeneral) totalConceptosGeneral.value = totalGeneral.toFixed(2);

            const precioPublicoInput = document.getElementById('precio_publico');
            if (precioPublicoInput) {
                precioPublicoInput.value = totalGeneral > 0 ? totalGeneral.toFixed(2) : '';
            }
        }

        function obtenerInsumoPorId(insumoId) {
            return insumosDisponibles.find(insumo => String(insumo.id) === String(insumoId));
        }

        function obtenerProductoPorId(productoId) {
            return productosDisponibles.find(producto => String(producto.id) === String(productoId));
        }

        function actualizarPrecioFilaInsumo(fila, insumoId) {
            const precioInput = fila?.querySelector('.insumo-precio');
            const insumo = obtenerInsumoPorId(insumoId);
            const precio = insumo?.precio_publico ?? '';

            if (precioInput) {
                precioInput.value = precio;
            }

            if (fila) {
                actualizarSubtotalFila(fila, 'insumo');
            }
        }

        function actualizarPrecioFilaProducto(fila, productoId) {
            const precioInput = fila?.querySelector('.producto-precio');
            const producto = obtenerProductoPorId(productoId);
            const precio = producto?.precio_publico ?? producto?.precio ?? '';

            if (precioInput) {
                precioInput.value = precio;
            }

            if (fila) {
                actualizarSubtotalFila(fila, 'producto');
            }
        }

        function obtenerOpcionesCortinero(selectedId = '') {
            const opciones = cortinerosTabDisponibles.map(cortinero => {
                const selected = String(cortinero.id) === String(selectedId) ? 'selected' : '';
                return `<option value="${cortinero.id}" data-precio="${cortinero.precio ?? ''}" ${selected}>${etiquetaCortinero(cortinero)}</option>`;
            }).join('');

            return `<option value="">Seleccione un cortinero</option>${opciones}`;
        }

        function obtenerOpcionesInsumoMaterialesVarios(selectedId = '') {
            const opciones = insumosMaterialesVarios.map(insumo => {
                const selected = String(insumo.id) === String(selectedId) ? 'selected' : '';
                return `<option value="${insumo.id}" data-precio="${insumo.costo ?? ''}" ${selected}>${etiquetaClaveNombre(insumo)}</option>`;
            }).join('');

            return `<option value="">Seleccione un insumo</option>${opciones}`;
        }

        function siguienteContadorOtrosDetalle(pane) {
            const actual = parseInt(pane.dataset.otrosCounter || '0', 10);
            const next = actual + 1;
            pane.dataset.otrosCounter = String(next);
            return next;
        }

        function htmlFilaOtroInsumoDetalle(index, rowNum, insumoId = '', cantidad = '', precio = '') {
            return `
                <tr class="detalle-otro-insumo-row">
                    <td>
                        <div class="d-flex align-items-center">
                            <select name="detalles[${index}][otros${rowNum}_nombre]" class="form-select detalle-otro-insumo-select select2 w-100">
                                ${obtenerOpcionesInsumoMaterialesVarios(insumoId)}
                            </select>
                            <button type="button" class="btn btn-sm btn-outline-danger detalle-eliminar-otro-insumo ml-2" title="Quitar renglón">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                    <td><input type="number" name="detalles[${index}][otros${rowNum}_cantidad]" class="form-control detalle-otro-insumo-cantidad" step="0.01" min="0" value="${cantidad}"></td>
                    <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][otros${rowNum}_precio]" class="form-control detalle-otro-insumo-precio" step="0.01" readonly value="${precio}"></div></td>
                    <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-otro-insumo-subtotal" step="0.01" readonly></div></td>
                </tr>
            `;
        }

        function añadirOtroInsumoDetalle(pane, index) {
            const tbody = pane.querySelector('.detalle-materiales-tbody');
            const totalRow = pane.querySelector('.detalle-materiales-total-row');
            if (!tbody || !totalRow) {
                return;
            }

            const rowNum = siguienteContadorOtrosDetalle(pane);
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = htmlFilaOtroInsumoDetalle(index, rowNum);
            const tr = wrapper.firstElementChild;
            tbody.insertBefore(tr, totalRow);

            const card = pane.querySelector('.detalle-materiales-varios-card');
            inicializarSelect2MaterialesVarios(tr.querySelector('.detalle-otro-insumo-select'), card || pane);
            recalcularPestanaDetalle(index, pane);
        }

        function restaurarMaterialesVariosDetalle(index, pane, filas) {
            if (!Array.isArray(filas) || !filas.length) {
                return;
            }

            const tbody = pane.querySelector('.detalle-materiales-tbody');
            const totalRow = pane.querySelector('.detalle-materiales-total-row');
            if (!tbody || !totalRow) {
                return;
            }

            const card = pane.querySelector('.detalle-materiales-varios-card');
            filas.forEach(fila => {
                const rowNum = siguienteContadorOtrosDetalle(pane);
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = htmlFilaOtroInsumoDetalle(
                    index,
                    rowNum,
                    fila.insumo_id ?? '',
                    fila.cantidad ?? '',
                    fila.precio_unitario ?? ''
                );
                const tr = wrapper.firstElementChild;
                tbody.insertBefore(tr, totalRow);
                inicializarSelect2MaterialesVarios(tr.querySelector('.detalle-otro-insumo-select'), card || pane);
            });
        }

        function construirSeccionTelaManoObra(index) {
            const camposOcultos = `
                <div class="d-none">
                    <input type="number" name="detalles[${index}][total_tela]" step="0.01">
                    <input type="number" name="detalles[${index}][precio_m2_tela]" step="0.01" value="100.00">
                    <input type="number" name="detalles[${index}][total_tela_final]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][total_tergal]" step="0.01">
                    <input type="number" name="detalles[${index}][precio_m2_tergal]" step="0.01" value="70.00">
                    <input type="number" name="detalles[${index}][total_tergal_final]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][total_forro]" step="0.01">
                    <input type="number" name="detalles[${index}][precio_m2_forro]" step="0.01" value="35.00">
                    <input type="number" name="detalles[${index}][total_final_forro]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][costo_total_tela_tergal_forro]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][m2_1]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][costo_mano_obra_1]" step="0.01" value="${manoObraBase.cortina}">
                    <input type="number" name="detalles[${index}][total_mano_obra_1]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][m2_2]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][costo_mano_obra_2]" step="0.01" value="${manoObraBase.tergal}">
                    <input type="number" name="detalles[${index}][total_mano_obra_2]" step="0.01" readonly>
                    <input type="number" name="detalles[${index}][costo_total_mano_obra]" step="0.01" readonly>
                </div>
            `;

            if (!verDetalleTelaManoObra) {
                return camposOcultos;
            }

            return `
                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h4 class="mb-1">Totales Tela, Tergal y Forro</h4>
                        <div class="text-muted small">Concentrado de materiales textiles para esta pestana.</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Descripción</th>
                                        <th>M²</th>
                                        <th>Precio m²</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cortina</td>
                                        <td><input type="number" name="detalles[${index}][total_tela]" class="form-control" step="0.01"></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][precio_m2_tela]" class="form-control" step="0.01" value="100.00"></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][total_tela_final]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr>
                                        <td>Tergal</td>
                                        <td><input type="number" name="detalles[${index}][total_tergal]" class="form-control" step="0.01"></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][precio_m2_tergal]" class="form-control" step="0.01" value="70.00"></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][total_tergal_final]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr>
                                        <td>Forro</td>
                                        <td><input type="number" name="detalles[${index}][total_forro]" class="form-control" step="0.01"></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][precio_m2_forro]" class="form-control" step="0.01" value="35.00"></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][total_final_forro]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Costo total tela, tergal y forro:</strong></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][costo_total_tela_tergal_forro]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h4 class="mb-1">Mano de Obra</h4>
                        <div class="text-muted small">Costos operativos calculados para esta pestana.</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr><th>m²</th><th>Costo Mano de Obra</th><th>Total</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><div class="d-flex align-items-center"><label class="me-2 mb-0" style="margin-right: 0.6rem;">Cortina</label><input type="number" name="detalles[${index}][m2_1]" class="form-control" step="0.01" readonly></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][costo_mano_obra_1]" class="form-control" step="0.01" value="${manoObraBase.cortina}"></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][total_mano_obra_1]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr>
                                        <td><div class="d-flex align-items-center"><label class="me-2 mb-0" style="margin-right: 1rem;">Tergal</label><input type="number" name="detalles[${index}][m2_2]" class="form-control" step="0.01" readonly></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][costo_mano_obra_2]" class="form-control" step="0.01" value="${manoObraBase.tergal}"></div></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][total_mano_obra_2]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-end"><strong>Costo Total Mano de Obra:</strong></td>
                                        <td><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][costo_total_mano_obra]" class="form-control" step="0.01" readonly></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        function construirSeccionTotalesDetalle(index) {
            const filaUtilidad = veUtilidadCotizacion
                ? `<tr><td><strong>Utilidad</strong></td><td><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-utilidad" step="0.01" readonly></div></td></tr>`
                : `<input type="hidden" class="detalle-utilidad">`;

            const filaCostoCortina = veCostosCotizacion
                ? `<tr><td><strong>Costo Cortina</strong></td><td><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-costo-cortina" step="0.01" readonly></div></td></tr>`
                : `<input type="hidden" class="detalle-costo-cortina">`;

            const filaCostoDecorador = veCostosCotizacion
                ? `<tr><td><strong>Costo Decorador</strong></td><td><div class="input-group"><input type="number" name="detalles[${index}][decorador_porcentaje]" class="form-control text-end detalle-decorador-porcentaje" value="15" min="0" max="100" step="0.01" style="max-width: 100px;"><span class="input-group-text">%</span><span class="input-group-text" style="margin-left: 0.5rem;">$</span><input type="number" class="form-control detalle-costo-decorador" step="0.01" readonly></div></td></tr>`
                : `<input type="hidden" name="detalles[${index}][decorador_porcentaje]" class="detalle-decorador-porcentaje" value="15"><input type="hidden" class="detalle-costo-decorador">`;

            return `
                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h4 class="mb-1">Totales</h4>
                        <div class="text-muted small">Resultado economico de esta pestana.</div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered mb-0 align-middle">
                                    <tbody>
                                        <tr><td><strong>Total No. Lienzos</strong></td><td><input type="number" class="form-control detalle-total-lienzos" step="0.01" readonly></td></tr>
                                        <tr><td><strong>Total m² Forro</strong></td><td><input type="number" class="form-control detalle-total-m2-forro" step="0.01" readonly></td></tr>
                                        <tr><td><strong>Total m² Tela</strong></td><td><input type="number" class="form-control detalle-total-m2-tela" step="0.01" readonly></td></tr>
                                        <tr><td><strong>Total m² Tergal</strong></td><td><input type="number" class="form-control detalle-total-m2-tergal" step="0.01" readonly></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered mb-0 align-middle">
                                    <tbody>
                                        ${filaCostoCortina}
                                        ${filaUtilidad}
                                        ${filaCostoDecorador}
                                        <tr><td><strong>Precio Publico</strong></td><td><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-precio-publico" step="0.01" readonly></div></td></tr>
                                        <tr><td></td><td><div class="row justify-content-end"><div class="col-md-12"><label class="form-label mb-0">Descuento (%)</label><input type="number" name="detalles[${index}][descuento]" class="form-control detalle-descuento" min="0" max="100" step="0.01" value="0"></div></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function construirTarjetasDetalle(index) {
            return `
                ${construirSeccionTelaManoObra(index)}

                <div class="card mt-4 border-0 shadow-sm detalle-materiales-varios-card">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h4 class="mb-1">Materiales Varios</h4>
                        <div class="text-muted small">Cortineros y materiales complementarios vinculados a esta pestana.</div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle materiales-varios-table">
                                <thead class="table-light">
                                    <tr><th style="width: 40%;">Material</th><th>Cantidad</th><th class="${claseOcultarPrecioMateriales}">Precio Unitario</th><th class="${claseOcultarPrecioMateriales}">Subtotal</th></tr>
                                </thead>
                                <tbody class="detalle-materiales-tbody">
                                    <tr>
                                        <td>
                                            <div class="cotinero-row-label mb-2">Cortinero cortina</div>
                                            <select name="detalles[${index}][cortinero_id]" class="form-select detalle-cortinero-select select2 w-100">
                                                ${obtenerOpcionesCortinero()}
                                            </select>
                                        </td>
                                        <td><input type="number" name="detalles[${index}][cortinero_cantidad]" class="form-control detalle-cortinero-cantidad" step="1" value="1" readonly></td>
                                        <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][cortinero_precio]" class="form-control detalle-cortinero-precio" step="0.01" readonly></div></td>
                                        <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-cortinero-subtotal" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="cotinero-row-label mb-2">Cortinero tergal</div>
                                            <select name="detalles[${index}][cortinero_tergal_id]" class="form-select detalle-cortinero-tergal-select select2 w-100">
                                                ${obtenerOpcionesCortinero()}
                                            </select>
                                        </td>
                                        <td><input type="number" name="detalles[${index}][cortinero_tergal_cantidad]" class="form-control detalle-cortinero-tergal-cantidad" step="1" value="1" readonly></td>
                                        <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" name="detalles[${index}][cortinero_tergal_precio]" class="form-control detalle-cortinero-tergal-precio" step="0.01" readonly></div></td>
                                        <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-cortinero-tergal-subtotal" step="0.01" readonly></div></td>
                                    </tr>
                                    <tr class="detalle-materiales-total-row">
                                        <td colspan="${vePreciosMaterialesVarios ? 3 : 2}" class="text-end"><strong>Costo Total Materiales:</strong></td>
                                        <td class="${claseOcultarPrecioMateriales}"><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control detalle-materiales-total" step="0.01" readonly></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2 detalle-anadir-otro-insumo"><i class="fas fa-plus"></i> Añadir otro insumo</button>
                    </div>
                </div>

                ${construirSeccionTotalesDetalle(index)}
            `;
        }

        function getDetallePanes() {
            return Array.from(tabsContent.querySelectorAll('[data-detalle-pane="1"]'));
        }

        function sincronizarAjustesGlobales(origenPane) {
            const decorador = origenPane.querySelector('.detalle-decorador-porcentaje')?.value ?? '15';
            const descuento = origenPane.querySelector('.detalle-descuento')?.value ?? '0';

            getDetallePanes().forEach(pane => {
                if (pane === origenPane) return;
                const decoradorInput = pane.querySelector('.detalle-decorador-porcentaje');
                const descuentoInput = pane.querySelector('.detalle-descuento');
                if (decoradorInput) decoradorInput.value = decorador;
                if (descuentoInput) descuentoInput.value = descuento;
            });

            const globalDecorador = document.getElementById('decorador_porcentaje');
            const globalDescuento = document.getElementById('descuento');
            if (globalDecorador) globalDecorador.value = decorador;
            if (globalDescuento) globalDescuento.value = descuento;
        }

        function actualizarTotalesGlobales() {
            let totalLienzos = 0;
            let totalM2Forro = 0;
            let totalM2Tela = 0;
            let totalM2Tergal = 0;
            let costoCortina = 0;
            let utilidad = 0;
            let costoDecorador = 0;
            let precioPublico = 0;

            getDetallePanes().forEach(pane => {
                totalLienzos += parseFloat(pane.querySelector('.detalle-total-lienzos')?.value) || 0;
                totalM2Forro += parseFloat(pane.querySelector('.detalle-total-m2-forro')?.value) || 0;
                totalM2Tela += parseFloat(pane.querySelector('.detalle-total-m2-tela')?.value) || 0;
                totalM2Tergal += parseFloat(pane.querySelector('.detalle-total-m2-tergal')?.value) || 0;
                costoCortina += parseFloat(pane.querySelector('.detalle-costo-cortina')?.value) || 0;
                utilidad += parseFloat(pane.querySelector('.detalle-utilidad')?.value) || 0;
                costoDecorador += parseFloat(pane.querySelector('.detalle-costo-decorador')?.value) || 0;
                precioPublico += parseFloat(pane.querySelector('.detalle-precio-publico')?.value) || 0;
            });

            const mappings = {
                total_lienzos: totalLienzos,
                total_m2_forro: totalM2Forro,
                total_m2_tela: totalM2Tela,
                total_m2_tergal: totalM2Tergal,
                costo_cortina: costoCortina,
                utilidad: utilidad,
                costo_decorador: costoDecorador
            };

            Object.entries(mappings).forEach(([id, value]) => {
                const input = document.getElementById(id);
                if (input) input.value = value > 0 ? value.toFixed(2) : '';
            });

            actualizarTotalesConceptos();
        }

        function renumerarPestanasDetalle() {
            getDetallePanes().forEach((pane, idx) => {
                const tabLink = tabsNav.querySelector(`[href="#${pane.id}"] .detalle-tab-label`) || tabsNav.querySelector(`[href="#${pane.id}"]`);
                const labelTarget = tabLink?.classList?.contains('detalle-tab-label') ? tabLink : tabLink?.querySelector?.('.detalle-tab-label');
                if (labelTarget) {
                    labelTarget.textContent = `Cortina/Tergal (${idx + 1})`;
                }
            });
        }

        function actualizarAccionesPestanasDetalle() {
            const detalleTabs = tabsNav.querySelectorAll('[data-detalle-tab="1"]');
            const mostrarEliminar = detalleTabs.length > 1;
            detalleTabs.forEach(tab => {
                const removeBtn = tab.querySelector('.remove-detalle-tab');
                if (removeBtn) {
                    removeBtn.classList.toggle('d-none', !mostrarEliminar);
                }
            });
            renumerarPestanasDetalle();
        }

        function sincronizarAnchosMaterialDesdeSelect(pane, index, selectEl) {
            if (!selectEl?.name) {
                return;
            }

            const field = (name) => pane.querySelector(`[name="detalles[${index}][${name}]"]`);
            const limpiarNumero = (valor) => valor.toString().replace(/[^\d.]/g, '');

            if (selectEl.name.endsWith('[tela_id]')) {
                const campo1 = selectEl.selectedOptions[0]?.dataset?.campo1 || '';
                const anchoTela = field('ancho_tela');
                if (campo1 && anchoTela) {
                    anchoTela.value = limpiarNumero(campo1);
                }
            }

            if (selectEl.name.endsWith('[tergal_id]')) {
                const campo1 = selectEl.selectedOptions[0]?.dataset?.campo1 || '';
                const anchoTergal = field('ancho_tergal');
                if (campo1 && anchoTergal) {
                    anchoTergal.value = limpiarNumero(campo1);
                }
            }

            if (selectEl.name.endsWith('[forro_id]')) {
                const campo1 = selectEl.selectedOptions[0]?.dataset?.campo1 || '';
                const anchoForro = field('ancho_forro');
                if (campo1 && anchoForro) {
                    anchoForro.value = limpiarNumero(campo1);
                }
            }
        }

        function recalcularPestanaDetalle(index, pane) {
            const field = (name) => pane.querySelector(`[name="detalles[${index}][${name}]"]`);
            const num = (name) => parseFloat(field(name)?.value) || 0;
            const set = (name, value) => { const input = field(name); if (input) input.value = value; };

            const telaSelect = field('tela_id');
            const tergalSelect = field('tergal_id');
            const forroSelect = field('forro_id');
            const cortinaChecked = !!pane.querySelector(`input[name="detalles[${index}][tipo][]"][value="cortina"]`)?.checked;
            const tergalChecked = !!pane.querySelector(`input[name="detalles[${index}][tipo][]"][value="tergal"]`)?.checked;
            const forroChecked = !!field('lleva_forro')?.checked;

            if (telaSelect) {
                const selected = telaSelect.selectedOptions[0];
                const precio = parseFloat(selected?.dataset?.precio) || num('precio_m2_tela');
                set('precio_m2_tela', precio ? precio.toFixed(2) : '');
            }

            if (tergalSelect) {
                const selected = tergalSelect.selectedOptions[0];
                const precio = parseFloat(selected?.dataset?.precio) || num('precio_m2_tergal');
                set('precio_m2_tergal', precio ? precio.toFixed(2) : '');
            }

            if (forroSelect) {
                const selected = forroSelect.selectedOptions[0];
                const precio = parseFloat(selected?.dataset?.precio) || num('precio_m2_forro');
                set('precio_m2_forro', precio ? precio.toFixed(2) : '');
            }

            if (cortinaChecked) {
                const ancho = num('ancho');
                const anchoTela = num('ancho_tela');
                const largo = num('largo');
                const bastilla = num('valor_bastilla');
                const lienzos = ancho > 0 && anchoTela > 0 ? (ancho * 2.5) / anchoTela : 0;
                const lienzosRedondeado = lienzos > 0 ? Math.ceil(lienzos) : 0;
                set('no_lienzos', lienzos ? lienzos.toFixed(2) : '');
                set('no_lienzos_redondeado', lienzosRedondeado || '');
                const totalTela = lienzosRedondeado > 0 ? lienzosRedondeado * (largo + bastilla) : 0;
                set('total_tela', totalTela ? totalTela.toFixed(2) : '');
                set('total_tela_final', totalTela ? (totalTela * num('precio_m2_tela')).toFixed(2) : '');
                set('m2_1', totalTela ? totalTela.toFixed(2) : '');
                const costoMO1 = anchoTela > 0 ? ((anchoTela <= 10 ? anchoTela * 100 : anchoTela) >= 280 ? manoObraBase.cortina * 2 : manoObraBase.cortina) : num('costo_mano_obra_1') || manoObraBase.cortina;
                set('costo_mano_obra_1', costoMO1.toFixed(2));
                set('total_mano_obra_1', totalTela ? (totalTela * costoMO1).toFixed(2) : '');
            } else {
                ['no_lienzos','no_lienzos_redondeado','total_tela','total_tela_final','m2_1','total_mano_obra_1'].forEach(name => set(name, ''));
            }

            if (tergalChecked) {
                if (cortinaChecked) {
                    const anchoCortina = field('ancho')?.value;
                    const largoCortina = field('largo')?.value;
                    if (anchoCortina) {
                        field('ancho_tergal_real').value = anchoCortina;
                    }
                    if (largoCortina) {
                        field('largo_tergal').value = parseFloat(largoCortina).toFixed(2);
                    }
                }
                const anchoReal = num('ancho_tergal_real');
                const anchoTela = num('ancho_tergal');
                const largo = num('largo_tergal');
                const bastilla = num('valor_bastilla_tergal');
                const lienzos = anchoReal > 0 && anchoTela > 0 ? (anchoReal * 2.5) / anchoTela : 0;
                const lienzosRedondeado = lienzos > 0 ? Math.ceil(lienzos) : 0;
                set('no_lienzos_tergal', lienzos ? lienzos.toFixed(2) : '');
                set('no_lienzos_redondeado_tergal', lienzosRedondeado || '');
                const totalTergal = lienzosRedondeado > 0 ? lienzosRedondeado * (largo + bastilla) : 0;
                set('total_tergal', totalTergal ? totalTergal.toFixed(2) : '');
                set('total_tergal_final', totalTergal ? (totalTergal * num('precio_m2_tergal')).toFixed(2) : '');
                set('m2_2', totalTergal ? totalTergal.toFixed(2) : '');
                set('costo_mano_obra_2', (num('costo_mano_obra_2') || manoObraBase.tergal).toFixed(2));
                set('total_mano_obra_2', totalTergal ? (totalTergal * num('costo_mano_obra_2')).toFixed(2) : '');
            } else {
                ['no_lienzos_tergal','no_lienzos_redondeado_tergal','total_tergal','total_tergal_final','m2_2','total_mano_obra_2'].forEach(name => set(name, ''));
            }

            if (forroChecked) {
                const anchoCortina = field('ancho')?.value;
                const largoCortina = field('largo')?.value;
                const anchoTergal = field('ancho_tergal_real')?.value;
                const largoTergal = field('largo_tergal')?.value;
                const anchoBase = anchoCortina || anchoTergal;
                const largoBase = largoCortina || largoTergal;
                if (anchoBase) {
                    field('ancho_forro_real').value = anchoBase;
                }
                if (largoBase) {
                    field('largo_forro').value = parseFloat(largoBase).toFixed(2);
                }
                const anchoReal = num('ancho_forro_real');
                const anchoTela = num('ancho_forro');
                const largo = num('largo_forro');
                const bastilla = num('valor_bastilla_forro');
                const lienzos = anchoReal > 0 && anchoTela > 0 ? (anchoReal * 2.5) / anchoTela : 0;
                const lienzosRedondeado = lienzos > 0 ? Math.ceil(lienzos) : 0;
                set('no_lienzos_forro', lienzos ? lienzos.toFixed(2) : '');
                set('no_lienzos_redondeado_forro', lienzosRedondeado || '');
                const totalForro = lienzosRedondeado > 0 ? lienzosRedondeado * (largo + bastilla) : 0;
                set('total_forro', totalForro ? totalForro.toFixed(2) : '');
                set('total_final_forro', totalForro ? (totalForro * num('precio_m2_forro')).toFixed(2) : '');
            } else {
                ['no_lienzos_forro','no_lienzos_redondeado_forro','total_forro','total_final_forro'].forEach(name => set(name, ''));
            }

            const cortineroSelect = pane.querySelector('.detalle-cortinero-select');
            const cortineroPrecio = pane.querySelector('.detalle-cortinero-precio');
            const cortineroCantidadInput = pane.querySelector('.detalle-cortinero-cantidad');
            if (cortineroCantidadInput) {
                cortineroCantidadInput.value = 1;
            }
            const cortineroCantidad = 1;
            const cortineroPrecioVal = parseFloat(cortineroSelect?.selectedOptions[0]?.dataset?.precio) || parseFloat(cortineroPrecio?.value) || 0;
            if (cortineroPrecio) cortineroPrecio.value = cortineroPrecioVal ? cortineroPrecioVal.toFixed(2) : '';
            const cortineroSubtotal = cortineroCantidad * cortineroPrecioVal;
            const cortineroSubtotalInput = pane.querySelector('.detalle-cortinero-subtotal');
            if (cortineroSubtotalInput) cortineroSubtotalInput.value = cortineroSubtotal ? cortineroSubtotal.toFixed(2) : '';

            const cortineroTergalSelect = pane.querySelector('.detalle-cortinero-tergal-select');
            const cortineroTergalPrecio = pane.querySelector('.detalle-cortinero-tergal-precio');
            const cortineroTergalCantidadInput = pane.querySelector('.detalle-cortinero-tergal-cantidad');
            if (cortineroTergalCantidadInput) {
                cortineroTergalCantidadInput.value = 1;
            }
            const cortineroTergalCantidad = 1;
            const cortineroTergalPrecioVal = parseFloat(cortineroTergalSelect?.selectedOptions[0]?.dataset?.precio) || parseFloat(cortineroTergalPrecio?.value) || 0;
            if (cortineroTergalPrecio) cortineroTergalPrecio.value = cortineroTergalPrecioVal ? cortineroTergalPrecioVal.toFixed(2) : '';
            const cortineroTergalSubtotal = cortineroTergalCantidad * cortineroTergalPrecioVal;
            const cortineroTergalSubtotalInput = pane.querySelector('.detalle-cortinero-tergal-subtotal');
            if (cortineroTergalSubtotalInput) cortineroTergalSubtotalInput.value = cortineroTergalSubtotal ? cortineroTergalSubtotal.toFixed(2) : '';

            let otrosMaterialesTotal = 0;
            pane.querySelectorAll('.detalle-otro-insumo-row').forEach(row => {
                const select = row.querySelector('.detalle-otro-insumo-select');
                const cantInput = row.querySelector('.detalle-otro-insumo-cantidad');
                const precioInput = row.querySelector('.detalle-otro-insumo-precio');
                const subtotalInput = row.querySelector('.detalle-otro-insumo-subtotal');
                const precioVal = parseFloat(select?.selectedOptions[0]?.dataset?.precio) || parseFloat(precioInput?.value) || 0;
                if (precioInput) {
                    precioInput.value = precioVal ? precioVal.toFixed(2) : '';
                }
                const cant = parseFloat(cantInput?.value) || 0;
                const sub = cant * precioVal;
                if (subtotalInput) {
                    subtotalInput.value = sub ? sub.toFixed(2) : '';
                }
                otrosMaterialesTotal += sub;
            });

            const materialesTotal = cortineroSubtotal + cortineroTergalSubtotal + otrosMaterialesTotal;
            const materialesTotalInput = pane.querySelector('.detalle-materiales-total');
            if (materialesTotalInput) materialesTotalInput.value = materialesTotal ? materialesTotal.toFixed(2) : '';

            const totalTelaFinal = num('total_tela_final');
            const totalTergalFinal = num('total_tergal_final');
            const totalForroFinal = num('total_final_forro');
            const costoTotalTela = totalTelaFinal + totalTergalFinal + totalForroFinal;
            set('costo_total_tela_tergal_forro', costoTotalTela ? costoTotalTela.toFixed(2) : '');

            const costoTotalMO = (parseFloat(field('total_mano_obra_1')?.value) || 0) + (parseFloat(field('total_mano_obra_2')?.value) || 0);
            set('costo_total_mano_obra', costoTotalMO ? costoTotalMO.toFixed(2) : '');

            const localTotalLienzos = (parseFloat(field('no_lienzos_redondeado')?.value) || 0) + (parseFloat(field('no_lienzos_redondeado_tergal')?.value) || 0) + (parseFloat(field('no_lienzos_redondeado_forro')?.value) || 0);
            const localM2Tela = parseFloat(field('total_tela')?.value) || 0;
            const localM2Tergal = parseFloat(field('total_tergal')?.value) || 0;
            const localM2Forro = parseFloat(field('total_forro')?.value) || 0;
            const localCostoCortina = costoTotalTela + costoTotalMO + materialesTotal;
            const localUtilidad = localCostoCortina * 0.15;
            const decoradorPorcentaje = parseFloat(field('decorador_porcentaje')?.value) || 15;
            const localCostoDecorador = localCostoCortina * (1 + decoradorPorcentaje / 100);
            const descuento = parseFloat(pane.querySelector('.detalle-descuento')?.value) || 0;
            const descripcionCompuesta = [
                field('descripcion_tela')?.value?.trim(),
                field('descripcion_tergal')?.value?.trim(),
                field('descripcion_forro')?.value?.trim()
            ].filter(Boolean).join(' | ');
            let localPrecioPublico = localCostoCortina * 2;
            if (descuento > 0) localPrecioPublico -= localPrecioPublico * (descuento / 100);

            set('descripcion', descripcionCompuesta);

            const setClassValue = (selector, value) => {
                const input = pane.querySelector(selector);
                if (input) input.value = value > 0 ? value.toFixed(2) : '';
            };

            setClassValue('.detalle-total-lienzos', localTotalLienzos);
            setClassValue('.detalle-total-m2-tela', localM2Tela);
            setClassValue('.detalle-total-m2-tergal', localM2Tergal);
            setClassValue('.detalle-total-m2-forro', localM2Forro);
            setClassValue('.detalle-costo-cortina', localCostoCortina);
            setClassValue('.detalle-utilidad', localUtilidad);
            setClassValue('.detalle-costo-decorador', localCostoDecorador);
            setClassValue('.detalle-precio-publico', localPrecioPublico);

            actualizarTotalesGlobales();
        }

        function inicializarPestanaDetalle(index, pane) {
            inicializarSelect2EnContenedor(pane);
            pane.addEventListener('input', function(event) {
                if (event.target.matches('input, select')) {
                    recalcularPestanaDetalle(index, pane);
                }
            });
            pane.addEventListener('change', function(event) {
                if (event.target.matches('input, select')) {
                    if (event.target.classList.contains('detalle-decorador-porcentaje') || event.target.classList.contains('detalle-descuento')) {
                        sincronizarAjustesGlobales(pane);
                    }
                    recalcularPestanaDetalle(index, pane);
                }
            });
            $(pane).on('change', 'select.select2', function() {
                sincronizarAnchosMaterialDesdeSelect(pane, index, this);
                recalcularPestanaDetalle(index, pane);
            });
            sincronizarAjustesGlobales(pane);
            recalcularPestanaDetalle(index, pane);
        }

        function crearPestanasFijas() {
            tabsNav.innerHTML = `
                <li class="nav-item" data-static-tab="insumos"><a class="nav-link" data-toggle="tab" href="#insumos-global">Insumos</a></li>
                <li class="nav-item" data-static-tab="productos"><a class="nav-link" data-toggle="tab" href="#productos-global">Productos</a></li>
            `;

            tabsContent.innerHTML = `
                <div class="tab-pane fade" id="insumos-global">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-primary add-insumo-row"><i class="fas fa-plus"></i> Agregar Insumo</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless insumos-table mb-0">
                            <tbody class="insumos-body"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <div style="min-width: 320px;">
                            <label class="font-weight-bold mb-1 d-block text-right">Total Insumos</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="total-insumos-tab" class="form-control text-right" step="0.01" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="productos-global">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-primary add-producto-row"><i class="fas fa-plus"></i> Agregar Producto</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless productos-table mb-0">
                            <tbody class="productos-body"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <div style="min-width: 320px;">
                            <label class="font-weight-bold mb-1 d-block text-right">Total Productos</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="total-productos-tab" class="form-control text-right" step="0.01" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const totalesPrevios = document.getElementById('totales-conceptos-wrapper');
            if (totalesPrevios) {
                totalesPrevios.remove();
            }

            tabsContent.insertAdjacentHTML('afterend', `
                <div class="border border-top-0 p-3" id="totales-conceptos-wrapper">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" value="1" id="aplicar_iva" name="aplicar_iva" ${aplicarIvaInicial ? 'checked' : ''}>
                                <label class="form-check-label" for="aplicar_iva">Aplicar IVA (16%)</label>
                            </div>
                        </div>
                        <div class="col-md-5 ms-auto">
                            <label class="font-weight-bold mb-1 d-block text-right">Total general</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="total-conceptos-general" class="form-control text-right" step="0.01" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            `);

            document.getElementById('aplicar_iva')?.addEventListener('change', function() {
                actualizarTotalesGlobales();
                actualizarTotalesConceptos();
            });

            const insumosBody = tabsContent.querySelector('.insumos-body');
            if (insumosBody) {
                insumosBody.innerHTML = crearFilaInsumo(0);
                inicializarSelect2EnContenedor(insumosBody);
            }

            const productosBody = tabsContent.querySelector('.productos-body');
            if (productosBody) {
                productosBody.innerHTML = crearFilaProducto(0);
                inicializarSelect2EnContenedor(productosBody);
            }

            actualizarTotalesConceptos();
        }

        function aplicarDatosInicialesDetalle(index, pane, datos) {
            if (!datos) {
                return;
            }

            const asignar = (name, value) => {
                if (value === null || value === undefined || value === '') {
                    return;
                }

                const field = pane.querySelector(`[name="detalles[${index}][${name}]"]`);
                if (field) {
                    field.value = value;
                }
            };

            const campos = {
                tela_id: datos.tela_id,
                ancho_tela: datos.ancho_tela,
                ancho: datos.ancho,
                largo: datos.largo,
                no_lienzos: datos.no_lienzos,
                no_lienzos_redondeado: datos.no_lienzos_redondeado,
                valor_bastilla: datos.bastilla,
                descripcion_tela: datos.descripcion_tela,
                tergal_id: datos.tergal_id,
                ancho_tergal: datos.ancho_tergal,
                ancho_tergal_real: datos.ancho_tergal_real,
                largo_tergal: datos.largo_tergal,
                no_lienzos_tergal: datos.no_lienzos_tergal,
                no_lienzos_redondeado_tergal: datos.no_lienzos_redondeado_tergal,
                valor_bastilla_tergal: datos.bastilla_tergal,
                descripcion_tergal: datos.descripcion_tergal,
                forro_id: datos.forro_id,
                ancho_forro: datos.ancho_forro,
                ancho_forro_real: datos.ancho_forro_real,
                largo_forro: datos.largo_forro,
                no_lienzos_forro: datos.no_lienzos_forro,
                no_lienzos_redondeado_forro: datos.no_lienzos_redondeado_forro,
                valor_bastilla_forro: datos.bastilla_forro,
                descripcion_forro: datos.descripcion_forro,
                total_tela: datos.total_tela,
                precio_m2_tela: datos.precio_m2_tela,
                total_tela_final: datos.total_tela_final,
                total_tergal: datos.total_tergal,
                precio_m2_tergal: datos.precio_m2_tergal,
                total_tergal_final: datos.total_tergal_final,
                total_forro: datos.total_forro,
                precio_m2_forro: datos.precio_m2_forro,
                total_final_forro: datos.total_final_forro,
                costo_total_tela_tergal_forro: datos.costo_total_tela_tergal_forro,
                m2_1: datos.m2_1,
                costo_mano_obra_1: datos.costo_mano_obra_1,
                total_mano_obra_1: datos.total_mano_obra_1,
                m2_2: datos.m2_2,
                costo_mano_obra_2: datos.costo_mano_obra_2,
                total_mano_obra_2: datos.total_mano_obra_2,
                costo_total_mano_obra: datos.costo_total_mano_obra,
                cortinero_id: datos.cortinero_id,
                cortinero_cantidad: 1,
                cortinero_precio: datos.cortinero_precio,
                cortinero_tergal_id: datos.cortinero_tergal_id,
                cortinero_tergal_cantidad: 1,
                cortinero_tergal_precio: datos.cortinero_tergal_precio,
                decorador_porcentaje: datos.decorador_porcentaje,
                descuento: datos.descuento,
            };

            Object.entries(campos).forEach(([name, value]) => asignar(name, value));

            restaurarMaterialesVariosDetalle(index, pane, datos.materiales_varios);

            const decoradorInput = pane.querySelector('.detalle-decorador-porcentaje');
            if (decoradorInput && datos.decorador_porcentaje) {
                decoradorInput.value = datos.decorador_porcentaje;
            }

            const descuentoInput = pane.querySelector('.detalle-descuento');
            if (descuentoInput) {
                descuentoInput.value = datos.descuento ?? 0;
            }

            pane.querySelectorAll('select.select2').forEach(select => {
                $(select).trigger('change.select2');
            });
        }

        function crearPestanaDetalle(index = detalleIndex++, datosIniciales = null) {
            const hasDetalle = tabsNav.querySelector('[data-detalle-tab]');
            const detalleTabId = `detalle-${index}`;
            const isFirst = !hasDetalle;

            const tabItem = document.createElement('li');
            tabItem.className = 'nav-item position-relative';
            tabItem.dataset.detalleTab = '1';
            tabItem.innerHTML = `
                <a class="nav-link pr-5 ${isFirst ? 'active' : ''}" data-toggle="tab" href="#${detalleTabId}">
                    <span class="detalle-tab-label">Cortina/Tergal (${index + 1})</span>
                </a>
                <button type="button" class="btn btn-link text-danger btn-sm px-2 remove-detalle-tab d-none position-absolute" data-target="#${detalleTabId}" title="Eliminar pestaña" style="top: 50%; right: 6px; transform: translateY(-50%); z-index: 2;">
                    <i class="fas fa-times"></i>
                </button>`;

            const insumosTab = tabsNav.querySelector('[data-static-tab="insumos"]');
            if (insumosTab) {
                tabsNav.insertBefore(tabItem, insumosTab);
            } else {
                tabsNav.appendChild(tabItem);
            }

            const pane = document.createElement('div');
            pane.className = `tab-pane fade ${isFirst ? 'show active' : ''}`;
            pane.dataset.detallePane = '1';
            pane.dataset.detalleIndex = String(index);
            pane.dataset.otrosCounter = '0';
            pane.id = detalleTabId;
            pane.innerHTML = `
                <div class="mb-3 border rounded p-3 bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de cotización</label>
                            <div class="cotizacion-type-options">
                                <label class="cotizacion-type-card" style="padding-left: 30px;">
                                    <input class="form-check-input" type="checkbox" name="detalles[${index}][tipo][]" value="cortina">
                                    <span class="form-check-label">&nbsp;Cortina</span>
                                </label>
                                <label class="cotizacion-type-card" style="padding-left: 30px;">
                                    <input class="form-check-input" type="checkbox" name="detalles[${index}][tipo][]" value="tergal">
                                    <span class="form-check-label">&nbsp;Tergal</span>
                                </label>
                                <label class="cotizacion-type-card" style="padding-left: 30px;">
                                    <input class="form-check-input" type="checkbox" name="detalles[${index}][lleva_forro]" value="1">
                                    <span class="form-check-label">&nbsp;Forro</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Área</label>
                            <input type="text" name="detalles[${index}][area]" class="form-control" placeholder="Ej. Cocina, Habitación, etc.">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="detalles[${index}][descripcion]" class="form-control" rows="4" placeholder="Se llenara automaticamente con las descripciones de tela, tergal y forro como sugerencia."></textarea>
                        </div>
                    </div>
                </div>
                <div class="detalle-wrapper"></div>
                <div class="detalle-totales-wrapper">${construirTarjetasDetalle(index)}</div>
            `;

            const insumosPane = tabsContent.querySelector('#insumos-global');
            if (insumosPane) {
                tabsContent.insertBefore(pane, insumosPane);
            } else {
                tabsContent.appendChild(pane);
            }

            const wrapper = pane.querySelector('.detalle-wrapper');

            if (datosIniciales) {
                if (datosIniciales.lleva_cortina) {
                    const cortinaCheck = pane.querySelector(`input[name="detalles[${index}][tipo][]"][value="cortina"]`);
                    if (cortinaCheck) cortinaCheck.checked = true;
                }
                if (datosIniciales.lleva_tergal) {
                    const tergalCheck = pane.querySelector(`input[name="detalles[${index}][tipo][]"][value="tergal"]`);
                    if (tergalCheck) tergalCheck.checked = true;
                }
                if (datosIniciales.lleva_forro) {
                    const forroCheck = pane.querySelector(`input[name="detalles[${index}][lleva_forro]"]`);
                    if (forroCheck) forroCheck.checked = true;
                }

                const areaInput = pane.querySelector(`[name="detalles[${index}][area]"]`);
                if (areaInput && datosIniciales.area) {
                    areaInput.value = datosIniciales.area;
                }

                const descripcionInput = pane.querySelector(`[name="detalles[${index}][descripcion]"]`);
                if (descripcionInput && datosIniciales.descripcion) {
                    descripcionInput.value = datosIniciales.descripcion;
                }
            }

            const actualizarDetalle = () => {
                const tipos = Array.from(pane.querySelectorAll(`input[name="detalles[${index}][tipo][]"]:checked`)).map(input => input.value);
                const llevaForro = !!pane.querySelector(`input[name="detalles[${index}][lleva_forro]"]`)?.checked;
                const valoresPrevios = {};

                wrapper.querySelectorAll('input, select, textarea').forEach(field => {
                    if (!field.name) {
                        return;
                    }

                    if (field.type === 'checkbox' || field.type === 'radio') {
                        valoresPrevios[field.name] = field.checked;
                        return;
                    }

                    valoresPrevios[field.name] = field.value;
                });

                wrapper.innerHTML = '';

                if (tipos.includes('cortina')) {
                    wrapper.insertAdjacentHTML('beforeend', `
                        <div class="card mt-2">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Cortina</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tela</label>
                                        <select name="detalles[${index}][tela_id]" class="form-control select2">
                                            <option value="">Seleccione una tela</option>
                                            @foreach($telas as $tela)
                                                @php
                                                    if($limpiarPrecio($tela->precio_publico) > 0) {
                                                        $precioTelaDetalle = $limpiarPrecio($tela->precio_publico);
                                                    } elseif($limpiarPrecio($tela->campo6) > 0) {
                                                        $precioTelaDetalle = $limpiarPrecio($tela->campo6);
                                                    } elseif($limpiarPrecio($tela->campo13) > 0) {
                                                        $precioTelaDetalle = $limpiarPrecio($tela->campo13);
                                                    } else {
                                                        $precioTelaDetalle = 100;
                                                    }
                                                @endphp
                                                <option value="{{ $tela->id }}" data-precio="{{ $precioTelaDetalle }}" data-campo1="{{ $tela->campo1Mostrar() }}">{{ $tela->etiquetaMaterialTextil() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Descripción</label>
                                        <input type="text" name="detalles[${index}][descripcion_tela]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ancho tela (cm)</label>
                                        <input type="number" name="detalles[${index}][ancho_tela]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ancho cortina (cm)</label>
                                        <input type="number" name="detalles[${index}][ancho]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Largo (m)</label>
                                        <input type="number" name="detalles[${index}][largo]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">No. Lienzos</label>
                                        <input type="number" name="detalles[${index}][no_lienzos]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">No. Lienzos redondeado</label>
                                        <input type="number" name="detalles[${index}][no_lienzos_redondeado]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Bastilla (m)</label>
                                        <input type="number" name="detalles[${index}][valor_bastilla]" class="form-control" step="0.01" value="0.40">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                }

                if (tipos.includes('tergal')) {
                    wrapper.insertAdjacentHTML('beforeend', `
                        <div class="card mt-2">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Tergal</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tergal</label>
                                        <select name="detalles[${index}][tergal_id]" class="form-control select2">
                                            <option value="">Seleccione un tergal</option>
                                            @foreach($tergales as $tergal)
                                                <option value="{{ $tergal->id }}" data-precio="{{ is_numeric($tergal->precio_publico) ? $tergal->precio_publico : 0 }}" data-campo1="{{ $tergal->campo1Mostrar() }}" data-campo2="{{ $tergal->campo2Mostrar() }}">{{ $tergal->etiquetaMaterialTextil() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Descripción</label>
                                        <input type="text" name="detalles[${index}][descripcion_tergal]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ancho tela (cm)</label>
                                        <input type="number" name="detalles[${index}][ancho_tergal]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ancho tergal (cm)</label>
                                        <input type="number" name="detalles[${index}][ancho_tergal_real]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Largo (m)</label>
                                        <input type="number" name="detalles[${index}][largo_tergal]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">No. Lienzos</label>
                                        <input type="number" name="detalles[${index}][no_lienzos_tergal]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">No. Lienzos redondeado</label>
                                        <input type="number" name="detalles[${index}][no_lienzos_redondeado_tergal]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Bastilla (m)</label>
                                        <input type="number" name="detalles[${index}][valor_bastilla_tergal]" class="form-control" step="0.01" value="0.65">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                }

                if (llevaForro) {
                    wrapper.insertAdjacentHTML('beforeend', `
                        <div class="card mt-2">
                            <div class="card-header bg-light py-2"><h6 class="mb-0">Forro</h6></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Forro</label>
                                        <select name="detalles[${index}][forro_id]" class="form-control select2">
                                            <option value="">Seleccione un forro</option>
                                            @foreach($forros as $forro)
                                                <option value="{{ $forro->id }}" data-precio="{{ is_numeric($forro->precio_publico) ? $forro->precio_publico : 0 }}" data-campo1="{{ $forro->campo1Mostrar() }}" data-campo2="{{ $forro->campo2Mostrar() }}">{{ $forro->etiquetaMaterialTextil() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Descripción</label>
                                        <input type="text" name="detalles[${index}][descripcion_forro]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ancho forro (cm)</label>
                                        <input type="number" name="detalles[${index}][ancho_forro]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Ancho forro real (cm)</label>
                                        <input type="number" name="detalles[${index}][ancho_forro_real]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Largo (m)</label>
                                        <input type="number" name="detalles[${index}][largo_forro]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">No. Lienzos</label>
                                        <input type="number" name="detalles[${index}][no_lienzos_forro]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">No. Lienzos redondeado</label>
                                        <input type="number" name="detalles[${index}][no_lienzos_redondeado_forro]" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Bastilla (m)</label>
                                        <input type="number" name="detalles[${index}][valor_bastilla_forro]" class="form-control" step="0.01" value="0.40">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                }

                [wrapper.querySelector(`select[name="detalles[${index}][tela_id]"]`), wrapper.querySelector(`select[name="detalles[${index}][tergal_id]"]`), wrapper.querySelector(`select[name="detalles[${index}][forro_id]"]`)].filter(Boolean).forEach(select => reinicializarSelect2(select));

                wrapper.querySelectorAll('input, select, textarea').forEach(field => {
                    if (!field.name || !Object.prototype.hasOwnProperty.call(valoresPrevios, field.name)) {
                        return;
                    }

                    if (field.type === 'checkbox' || field.type === 'radio') {
                        field.checked = !!valoresPrevios[field.name];
                        return;
                    }

                    field.value = valoresPrevios[field.name];
                    if (field.tagName === 'SELECT') {
                        $(field).trigger('change');
                    }
                });
            };

            pane.querySelectorAll(`input[name="detalles[${index}][tipo][]"], input[name="detalles[${index}][lleva_forro]"]`).forEach(input => {
                input.addEventListener('change', actualizarDetalle);
            });
            actualizarDetalle();
            if (datosIniciales) {
                aplicarDatosInicialesDetalle(index, pane, datosIniciales);
            }
            inicializarPestanaDetalle(index, pane);
            actualizarAccionesPestanasDetalle();
            return pane;
        }

        function cargarInsumosExistentes() {
            const insumosBody = tabsContent.querySelector('.insumos-body');
            if (!insumosBody || !insumosExistentes.length) {
                return;
            }

            insumosBody.innerHTML = '';
            insumosExistentes.forEach((insumo, index) => {
                insumosBody.insertAdjacentHTML('beforeend', crearFilaInsumo(
                    index,
                    insumo.tipo_id ?? '',
                    insumo.id ?? '',
                    insumo.cantidad ?? '',
                    insumo.precio ?? '',
                    insumo.descuento ?? '0'
                ));
            });
            inicializarSelect2EnContenedor(insumosBody);
            insumosExistentes.forEach((insumo, index) => {
                const fila = insumosBody.children[index];
                if (fila) {
                    actualizarPrecioFilaInsumo(fila, insumo.id);
                    actualizarSubtotalFila(fila, 'insumo');
                }
            });
            actualizarTotalesConceptos();
        }

        function cargarProductosExistentes() {
            const productosBody = tabsContent.querySelector('.productos-body');
            if (!productosBody || !productosExistentes.length) {
                return;
            }

            productosBody.innerHTML = '';
            productosExistentes.forEach((producto, index) => {
                productosBody.insertAdjacentHTML('beforeend', crearFilaProducto(
                    index,
                    producto.tipo_id ?? '',
                    producto.id ?? '',
                    producto.cantidad ?? '',
                    producto.precio ?? '',
                    producto.descuento ?? '0'
                ));
            });
            inicializarSelect2EnContenedor(productosBody);
            productosExistentes.forEach((producto, index) => {
                const fila = productosBody.children[index];
                if (fila) {
                    actualizarPrecioFilaProducto(fila, producto.id);
                    actualizarSubtotalFila(fila, 'producto');
                }
            });
            actualizarTotalesConceptos();
        }

        function cargarDetallesExistentes() {
            if (!detallesExistentes.length) {
                crearPestanaDetalle();
                return;
            }

            detallesExistentes.forEach((detalle, index) => {
                detalleIndex = index;
                crearPestanaDetalle(index, detalle);
            });
            detalleIndex = detallesExistentes.length;
            actualizarTotalesGlobales();
        }

        function agregarFilaDinamica(tipo) {
            const body = tabsContent.querySelector(tipo === 'insumos' ? '.insumos-body' : '.productos-body');
            const index = body.children.length;

            if (tipo === 'insumos') {
                body.insertAdjacentHTML('beforeend', crearFilaInsumo(index));
                inicializarSelect2EnContenedor(body.lastElementChild);
                actualizarTotalesConceptos();
                return;
            }

            if (tipo === 'productos') {
                body.insertAdjacentHTML('beforeend', crearFilaProducto(index));
                inicializarSelect2EnContenedor(body.lastElementChild);
                actualizarTotalesConceptos();
                return;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="${tipo}[${index}][id]" class="form-control">
                        <option value="">Seleccione un ${tipo === 'insumos' ? 'insumo' : 'producto'}</option>
                        ${tipo === 'insumos' ? `@foreach($insumos as $insumo)<option value="{{ $insumo->id }}">{{ $insumo->etiquetaCotizacion() }}</option>@endforeach` : `@foreach($productos as $producto)<option value="{{ $producto->id }}">{{ $producto->etiquetaCotizacion() }}</option>@endforeach`}
                    </select>
                </td>
                <td><input type="number" name="${tipo}[${index}][cantidad]" class="form-control" step="0.01"></td>
                <td><input type="number" name="${tipo}[${index}][precio]" class="form-control" step="0.01"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row">-</button></td>
            `;
            body.appendChild(row);
            row.querySelector('.remove-row').addEventListener('click', () => row.remove());
        }

        crearPestanasFijas();
        cargarInsumosExistentes();
        cargarProductosExistentes();
        cargarDetallesExistentes();

        document.getElementById('agregar-cotizacion-btn').addEventListener('click', () => {
            crearPestanaDetalle();
        });

        tabsNav.addEventListener('click', function(event) {
            const removeBtn = event.target.closest('.remove-detalle-tab');
            if (!removeBtn) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const targetSelector = removeBtn.dataset.target;
            const targetPane = targetSelector ? tabsContent.querySelector(targetSelector) : null;
            const detalleTabs = Array.from(tabsNav.querySelectorAll('[data-detalle-tab="1"]'));

            if (!targetPane || detalleTabs.length <= 1) {
                return;
            }

            swal({
                title: '¿Está seguro?',
                text: 'Se eliminará esta cotización Cortina/Tergal. ¿Deseas continuar?',
                icon: 'warning',
                buttons: ['Cancelar', 'Sí, eliminar'],
                dangerMode: true,
            }).then(function (confirmado) {
                if (!confirmado) {
                    return;
                }

                const currentTab = removeBtn.closest('[data-detalle-tab="1"]');
                const currentIndex = detalleTabs.indexOf(currentTab);
                const isActive = currentTab?.querySelector('.nav-link')?.classList.contains('active');

                currentTab?.remove();
                targetPane.remove();

                const remainingTabs = Array.from(tabsNav.querySelectorAll('[data-detalle-tab="1"]'));
                if (isActive && remainingTabs.length) {
                    const nextTab = remainingTabs[currentIndex] || remainingTabs[currentIndex - 1] || remainingTabs[0];
                    const nextLink = nextTab?.querySelector('.nav-link');
                    if (nextLink) {
                        $(nextLink).tab('show');
                    }
                }

                actualizarAccionesPestanasDetalle();
                actualizarTotalesGlobales();
            });
        });

        tabsContent.addEventListener('click', function(event) {
            if (event.target.classList.contains('add-insumo-row')) {
                agregarFilaDinamica('insumos');
            }

            if (event.target.classList.contains('add-producto-row')) {
                agregarFilaDinamica('productos');
            }

            if (event.target.classList.contains('remove-row')) {
                const fila = event.target.closest('tr');
                $(fila).find('select.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                fila?.remove();
                actualizarTotalesConceptos();
            }

            const addOtroInsumoBtn = event.target.closest('.detalle-anadir-otro-insumo');
            if (addOtroInsumoBtn) {
                const pane = addOtroInsumoBtn.closest('[data-detalle-pane="1"]');
                const index = parseInt(pane?.dataset.detalleIndex ?? '0', 10);
                if (pane) {
                    añadirOtroInsumoDetalle(pane, index);
                }
            }

            const removeOtroInsumoBtn = event.target.closest('.detalle-eliminar-otro-insumo');
            if (removeOtroInsumoBtn) {
                const pane = removeOtroInsumoBtn.closest('[data-detalle-pane="1"]');
                const index = parseInt(pane?.dataset.detalleIndex ?? '0', 10);
                const fila = removeOtroInsumoBtn.closest('tr');
                $(fila).find('select.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                fila?.remove();
                if (pane) {
                    recalcularPestanaDetalle(index, pane);
                }
            }
        });

        tabsContent.addEventListener('change', function(event) {
            if (event.target.classList.contains('insumo-tipo-select')) {
                const fila = event.target.closest('tr');
                const insumoSelect = fila?.querySelector('.insumo-select');
                const precioInput = fila?.querySelector('.insumo-precio');
                const subtotalInput = fila?.querySelector('.insumo-subtotal');

                if (insumoSelect) {
                    actualizarSelectDependiente(
                        insumoSelect,
                        obtenerOpcionesInsumo(event.target.value)
                    );
                }

                if (precioInput) {
                    precioInput.value = '';
                }

                if (subtotalInput) {
                    subtotalInput.value = '0.00';
                }

                actualizarTotalesConceptos();
            }

            if (event.target.classList.contains('insumo-select')) {
                const fila = event.target.closest('tr');
                actualizarPrecioFilaInsumo(fila, event.target.value);
            }

            if (event.target.classList.contains('producto-tipo-select')) {
                const fila = event.target.closest('tr');
                const productoSelect = fila?.querySelector('.producto-select');
                const precioInput = fila?.querySelector('.producto-precio');
                const subtotalInput = fila?.querySelector('.producto-subtotal');

                if (productoSelect) {
                    actualizarSelectDependiente(
                        productoSelect,
                        obtenerOpcionesProducto(event.target.value)
                    );
                }

                if (precioInput) {
                    precioInput.value = '';
                }

                if (subtotalInput) {
                    subtotalInput.value = '0.00';
                }

                if (fila) {
                    actualizarSubtotalFila(fila, 'producto');
                }
            }

            if (event.target.classList.contains('producto-select')) {
                const fila = event.target.closest('tr');
                actualizarPrecioFilaProducto(fila, event.target.value);
            }
        });

        $(tabsContent).on('change', '.insumo-select', function() {
            actualizarPrecioFilaInsumo(this.closest('tr'), this.value);
        });

        $(tabsContent).on('change', '.producto-select', function() {
            actualizarPrecioFilaProducto(this.closest('tr'), this.value);
        });

        tabsContent.addEventListener('input', function(event) {
            const fila = event.target.closest('tr');
            if (!fila) {
                return;
            }

            if (event.target.closest('.insumos-body') && (
                event.target.name?.includes('[cantidad]') ||
                event.target.name?.includes('[descuento]')
            )) {
                actualizarSubtotalFila(fila, 'insumo');
            }

            if (event.target.closest('.productos-body') && (
                event.target.name?.includes('[cantidad]') ||
                event.target.name?.includes('[descuento]')
            )) {
                actualizarSubtotalFila(fila, 'producto');
            }
        });

        const cortina = document.getElementById('cortinaCheck');
        const tergal = document.getElementById('tergalCheck');
        const forro = document.getElementById('forroCheck');
        const formDinamico = document.getElementById('form-dinamico');

        if (formDinamico && cortina && tergal && forro) {
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
                                <div class="card mt-4 border-0 shadow-sm">
                                    <div class="card-header bg-light border-bottom-0 py-3">
                                        <h4 class="mb-1">Datos de la Cortina</h4>
                                        <div class="text-muted small">Configuracion de tela, dimensiones y lienzos para este concepto.</div>
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
                                                        {{ $tela->etiquetaMaterialTextil() }}
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

                                reinicializarSelect2(telaSelect);

                                $(telaSelect).on('change', function() {
                                    const selected = $(this).find('option:selected');
                                    const precio = selected.data('precio');
                                    const campo1 = selected.data('campo1');
                                    $('#precio_m2_tela').val(Number(precio).toFixed(2)).trigger('input');
                                    if (campo1 !== undefined && campo1 !== null && campo1 !== '') {
                                        // Limpia el valor para dejar solo números y punto decimal
                                        let limpio = campo1.toString().replace(/[^\d.]/g, '');
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
                                <div class="card mt-4 border-0 shadow-sm">
                                    <div class="card-header bg-light border-bottom-0 py-3">
                                        <h4 class="mb-1">Datos del Tergal</h4>
                                        <div class="text-muted small">Medidas, ancho util y calculo de lienzos del tergal.</div>
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
                                                        data-campo1="{{ $tergal->campo1Mostrar() }}"
                                                        data-campo2="{{ $tergal->campo2Mostrar() }}"
                                                        {{ old('detalle.tergal_id', $detalleCotizacion->tergal_id ?? '') == $tergal->id ? 'selected' : '' }}>
                                                        {{ $tergal->etiquetaMaterialTextil() }}
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

                                reinicializarSelect2(tergalSelect);

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

                                    const largoTergal = parseFloat(largoTergalInput.value) || 0;
                                    const bastillaTergal = parseFloat(document.getElementById('valor_bastilla_tergal')?.value) || 0;
                                    const lienzosRedondeado = parseFloat(noLienzosRedondeadoTergalInput.value) || 0;
                                    const totalTergal = (!isNaN(lienzosRedondeado) && !isNaN(largoTergal)) ? (lienzosRedondeado * (largoTergal + bastillaTergal)) : 0;

                                    const totalTergalInput = document.getElementById('total_tergal');
                                    if (totalTergalInput) {
                                        totalTergalInput.value = totalTergal > 0 ? totalTergal.toFixed(2) : '';
                                    }

                                    const m2TergalInput = document.querySelector('[name="detalle[m2_2]"]');
                                    if (m2TergalInput) {
                                        m2TergalInput.value = totalTergal > 0 ? totalTergal.toFixed(2) : '';
                                    }

                                    const precioTergal = parseFloat(document.getElementById('precio_m2_tergal')?.value) || 0;
                                    const totalTergalFinalInput = document.getElementById('total_tergal_final');
                                    if (totalTergalFinalInput) {
                                        totalTergalFinalInput.value = (totalTergal * precioTergal).toFixed(2);
                                    }

                                    const totalTelaFinal = parseFloat(document.getElementById('total_tela_final')?.value) || 0;
                                    const totalForroFinal = parseFloat(document.getElementById('total_final_forro')?.value) || 0;
                                    const costoTotalTelaTergalForroInput = document.getElementById('costo_total_tela_tergal_forro');
                                    if (costoTotalTelaTergalForroInput) {
                                        costoTotalTelaTergalForroInput.value = (totalTelaFinal + (totalTergal * precioTergal) + totalForroFinal).toFixed(2);
                                    }

                                    actualizarPrecioManoObra();
                                    actualizarTablaTotales();
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

                                [anchoTergalRealInput, anchoTergalInput, largoTergalInput, noLienzosRedondeadoTergalInput].forEach(input => {
                                    input.addEventListener('input', calcularTergal);
                                });

                                sincronizarTergalConCortina();
                                calcularTergal();

                                $(tergalSelect).trigger('change');

                            }, 200);
                        }

                        if (forro.checked) {
                            formDinamico.innerHTML += `
                                <div class="card mt-4 border-0 shadow-sm">
                                    <div class="card-header bg-light border-bottom-0 py-3">
                                        <h4 class="mb-1">Datos del Forro</h4>
                                        <div class="text-muted small">Informacion de soporte para el forro y su calculo asociado.</div>
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
                                                        {{ $forro->etiquetaMaterialTextil() }}
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

                                reinicializarSelect2(forroSelect);

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

                                    const largoForro = parseFloat(largoForroInput.value) || 0;
                                    const bastillaForro = parseFloat(document.getElementById('valor_bastilla_forro')?.value) || 0;
                                    const lienzosRedondeado = parseFloat(noLienzosRedondeadoForroInput.value) || 0;
                                    const totalForro = (!isNaN(lienzosRedondeado) && !isNaN(largoForro)) ? (lienzosRedondeado * (largoForro + bastillaForro)) : 0;

                                    const totalForroInput = document.getElementById('total_forro');
                                    if (totalForroInput) {
                                        totalForroInput.value = totalForro > 0 ? totalForro.toFixed(2) : '';
                                    }

                                    const precioForro = parseFloat(document.getElementById('precio_m2_forro')?.value) || 0;
                                    const totalFinalForroInput = document.getElementById('total_final_forro');
                                    if (totalFinalForroInput) {
                                        totalFinalForroInput.value = (totalForro * precioForro).toFixed(2);
                                    }

                                    const totalTelaFinal = parseFloat(document.getElementById('total_tela_final')?.value) || 0;
                                    const totalTergalFinal = parseFloat(document.getElementById('total_tergal_final')?.value) || 0;
                                    const costoTotalTelaTergalForroInput = document.getElementById('costo_total_tela_tergal_forro');
                                    if (costoTotalTelaTergalForroInput) {
                                        costoTotalTelaTergalForroInput.value = (totalTelaFinal + totalTergalFinal + (totalForro * precioForro)).toFixed(2);
                                    }

                                    actualizarTablaTotales();
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

                                    calcularForro();
                                });

                                ['ancho', 'largo', 'ancho_tergal_real', 'largo_tergal'].forEach(id => {
                                    const input = document.getElementById(id);
                                    if (input) {
                                        input.addEventListener('input', sincronizarDimensionesForro);
                                    }
                                });

                                [anchoForroRealInput, anchoForroInput, largoForroInput].forEach(input => {
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
        }

        document.getElementById('cotizacion-form')?.addEventListener('submit', function(event) {
            const panes = getDetallePanes();
            let detallesValidos = 0;

            panes.forEach(pane => {
                const index = pane.id.replace('detalle-', '');
                const tieneTipo =
                    !!pane.querySelector(`input[name="detalles[${index}][tipo][]"][value="cortina"]:checked`) ||
                    !!pane.querySelector(`input[name="detalles[${index}][tipo][]"][value="tergal"]:checked`) ||
                    !!pane.querySelector(`input[name="detalles[${index}][lleva_forro]"]:checked`);

                if (tieneTipo) {
                    detallesValidos++;
                    return;
                }

                pane.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = true;
                });
            });

            const totalInsumos = sumarSubtotales('.insumo-subtotal');
            const totalProductos = sumarSubtotales('.producto-subtotal');
            const tieneConceptosExtra = totalInsumos > 0 || totalProductos > 0;

            if (detallesValidos === 0 && !tieneConceptosExtra) {
                event.preventDefault();
                alert('Debe agregar al menos un concepto (Cortina, Tergal o Forro) o registrar insumos/productos.');
                return;
            }

            actualizarTotalesConceptos();
        });

        reinicializarSelect2(document.getElementById('cliente_id'));

        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            cerrarSelect2Abiertos();
        });

        $(document).on('select2:open', function(event) {
            cerrarSelect2Abiertos(event.target);
        });
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
        if (typeof actualizarLargoTergal === 'function') {
            actualizarLargoTergal();
        }
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
            //manoObraTergalInput.value = (anchoTergalEnCm >= 280 ? valorBaseManoObraTergal * 2 : valorBaseManoObraTergal).toFixed(2);
            manoObraTergalInput.value = (valorBaseManoObraTergal).toFixed(2);

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
    const insumosDisponibles = @json($insumosMaterialesVarios ?? $insumos);
    const cortinerosDisponibles = @json($cortineros);
    const vePreciosMaterialesVariosLegacy = @json(auth()->user()->veCostosCotizacion());

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
            option.textContent = etiquetaClaveNombre(insumo);
            option.dataset.precio = insumo.costo ?? '';
            select.appendChild(option);
        });

        // Cortineros (optgroup para diferenciarlos)
        if (cortinerosDisponibles && cortinerosDisponibles.length > 0) {
            const cortineroGroup = document.createElement('optgroup');
            cortineroGroup.label = 'Cortineros';
            cortinerosDisponibles.forEach(cortinero => {
                const option = document.createElement('option');
                option.value = 'cortinero_' + cortinero.id;
                option.textContent = etiquetaCortinero(cortinero);
                option.dataset.precio = cortinero.precio ?? '';
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
        if (!vePreciosMaterialesVariosLegacy) {
            tdPrecio.classList.add('d-none');
        }

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
        if (!vePreciosMaterialesVariosLegacy) {
            tdSubtotal.classList.add('d-none');
        }

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

        reinicializarSelect2(selectInsumo);

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
            const cantidadInput = fila.querySelector('input[name*="_cantidad"]');
            const precioInput = fila.querySelector('input[name*="_precio"]');

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
        const costoCortinaInput = document.getElementById('costo_cortina');
        if (costoCortinaInput) {
            costoCortinaInput.value = costoCortina > 0 ? costoCortina.toFixed(2) : '';
        }

        // Utilidad
        const utilidad = costoCortina * 0.15;
        document.querySelectorAll('input[name="totales[utilidad]"]').forEach(function(input) {
            input.value = utilidad > 0 ? utilidad.toFixed(2) : '';
        });

        // Costo decorador
        const decoradorPorcentajeInput = document.getElementById('decorador_porcentaje');
        const decoradorPorcentaje = decoradorPorcentajeInput ? (parseFloat(decoradorPorcentajeInput.value) || 0) : 15;
        const costoDecorador = costoCortina * (1 + decoradorPorcentaje / 100);
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
        document.getElementById('decorador_porcentaje')?.addEventListener('input', actualizarTablaTotales);
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
        if (e.target.name && e.target.name.startsWith('detalle[otros')) {
            actualizarCostoTotal();
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
            const haySeleccion = !!(cortina?.checked || tergal?.checked || forro?.checked);
            tablas.forEach(tabla => {
                if (!tabla) {
                    return;
                }

                if (!verDetalleTelaManoObra && (tabla.id === 'tabla-totales-tela-tergal' || tabla.id === 'tabla-mano-obra')) {
                    tabla.classList.add('d-none');
                    return;
                }

                tabla.classList.toggle('d-none', !haySeleccion);
            });
        }

        if (cortina) cortina.addEventListener('change', mostrarOcultarTablas);
        if (tergal) tergal.addEventListener('change', mostrarOcultarTablas);
        if (forro) forro.addEventListener('change', mostrarOcultarTablas);

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
        const selected = $(this).find('option:selected');
        const precio = selected.data('precio');
        const campo1 = selected.data('campo1');
        $('#precio_m2_tela').val(Number(precio).toFixed(2));

        if (campo1 !== undefined && campo1 !== null && campo1 !== '') {
            $('#ancho_tela').val(campo1.toString().replace(/[^\d.]/g, ''));
        }

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

    /* function actualizarLargoTergal() {
        const largoCortinaInput = document.getElementById('largo');
        const largoTergalInput = document.getElementById('largo_tergal');
        const bastillaTergalInput = document.getElementById('valor_bastilla_tergal');
        
        if (!largoTergalInput) return;
        
        // Base: largo de cortina si existe, sino el valor actual del tergal (sin restar bastillas previas)
        let valorBase = 0;
        if (largoCortinaInput && largoCortinaInput.value) {
            valorBase = parseSafeFloat(largoCortinaInput.value);
        } else {
            valorBase = parseSafeFloat(largoTergalInput.value); // Usa el valor actual como base si no hay cortina
        }
        
        const bastilla = bastillaTergalInput ? parseSafeFloat(bastillaTergalInput.value) : 0;
        const nuevoValor = (valorBase + bastilla).toFixed(2);
        
        if (largoTergalInput.value !== nuevoValor) {
            largoTergalInput.value = nuevoValor;
            
            // Guardar último valor de bastilla para referencia futura
            if (bastillaTergalInput) {
                bastillaTergalInput.dataset.lastValue = bastilla;
            }
            
            const event = new Event('input', { bubbles: true });
            largoTergalInput.dispatchEvent(event);
        }
    } */

    // Función para actualizar forro (prioriza cortina, luego tergal, como sincronizarDimensionesForro)
    function actualizarLargoForro() {
        const largoCortinaInput = document.getElementById('largo');
        const largoTergalInput = document.getElementById('largo_tergal');
        const largoForroInput = document.getElementById('largo_forro');
        const bastillaForroInput = document.getElementById('valor_bastilla_forro');
        
        if (!largoForroInput) return;
        
        // Base: largo de cortina primero, luego tergal (consistente con sincronizarDimensionesForro)
        let valorBase = 0;
        if (largoCortinaInput && largoCortinaInput.value) {
            valorBase = parseSafeFloat(largoCortinaInput.value);
        } else if (largoTergalInput && largoTergalInput.value) {
            valorBase = parseSafeFloat(largoTergalInput.value);
        }
        
        const bastilla = bastillaForroInput ? parseSafeFloat(bastillaForroInput.value) : 0;
        const nuevoValor = (valorBase + bastilla).toFixed(2);
        
        if (largoForroInput.value !== nuevoValor) {
            largoForroInput.value = nuevoValor;
            
            // Guardar último valor de bastilla del forro
            if (bastillaForroInput) {
                bastillaForroInput.dataset.lastValue = bastilla;
            }
            
            const event = new Event('input', { bubbles: true });
            largoForroInput.dispatchEvent(event);
        }
    }


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
