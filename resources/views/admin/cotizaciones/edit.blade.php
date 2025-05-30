@extends('layouts.stisla')
@section('title', 'Editar Cotización')
@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Cotización</h1>
    </div>
    <div class="section-body">
        <form method="POST" action="{{ route('admin.cotizaciones.update', $cotizacion->id) }}">
            @csrf
            @method('PUT')

            <!-- Información General -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control" required autocomplete="off">
                        <option value="">Seleccione un cliente</option>
                        @foreach(\App\Models\Cliente::where('borrado', 0)->orderBy('nombre')->get() as $cliente)
                        <option value="{{ $cliente->id }}" {{ $cotizacion->cliente_id == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ $cotizacion->fecha }}">
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <div class="col d-flex align-items-center gap-3">
                    <label class="mb-0 me-3 align-middle" style="vertical-align: middle;">Tipo de Cotización:</label>
                    <div class="form-check form-check-inline" style="margin-left: 0.5rem;">
                        <input type="checkbox" id="cortinaCheck" name="tipo[]" value="cortina" class="form-check-input" autocomplete="off"
                            {{ $cotizacion->lleva_cortina ? 'checked' : '' }}>
                        <label class="form-check-label" for="cortinaCheck">Cortina</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="tergalCheck" name="tipo[]" value="tergal" class="form-check-input" autocomplete="off"
                            {{ $cotizacion->lleva_tergal ? 'checked' : '' }}>
                        <label class="form-check-label" for="tergalCheck">Tergal</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="forroCheck" name="lleva_forro" value="1" class="form-check-input" autocomplete="off"
                            {{ $cotizacion->lleva_forro ? 'checked' : '' }}>
                        <label class="form-check-label" for="forroCheck">Lleva Forro</label>
                    </div>
                </div>
            </div>

            <!-- Sección de Cortina -->
            @if(isset($cotizacion) && $cotizacion->lleva_cortina)
            <div class="card mt-4">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Detalle de Cortina</h4>
                </div>
                <div class="card-body pt-2">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="tela_id" class="mb-1">Tela</label>
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
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="ancho_tela">Ancho tela cortina</label>
                            <input type="text" name="detalle[ancho_tela]" id="ancho_tela" class="form-control"
                                value="{{ old('detalle.ancho_tela', $detalleCotizacion->ancho_tela ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="ancho">Ancho</label>
                            <input type="text" name="detalle[ancho]" id="ancho" class="form-control"
                                value="{{ old('detalle.ancho', $detalleCotizacion->ancho ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="largo">Largo</label>
                            <input type="text" name="detalle[largo]" id="largo" class="form-control"
                                value="{{ old('detalle.largo', $detalleCotizacion->largo ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="no_lienzos">No. Lienzos</label>
                            <input type="number" name="detalle[no_lienzos]" id="no_lienzos" class="form-control"
                                value="{{ old('detalle.no_lienzas', $detalleCotizacion->no_lienzos ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="no_lienzos_redondeado">No. Lienzos Redondeados</label>
                            <input type="number" name="detalle[no_lienzos_redondeado]" id="no_lienzos_redondeado" class="form-control"
                                value="{{ old('detalle.no_lienzos_redondeado', $detalleCotizacion->no_lienzos_redondeado ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="valor_bastilla">Bastilla</label>
                            <input type="number" id="valor_bastilla" name="detalle[valor_bastilla]" class="form-control" 
                                value="{{ old('detalle.valor_bastilla', $detalleCotizacion->bastilla ?? '') }}" 
                                placeholder="Ej. 1.10" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sección de Tergal -->
            @if(isset($cotizacion) && $cotizacion->lleva_tergal)
            <div class="card mt-4">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Detalle de Tergal</h4>
                </div>
                <div class="card-body pt-2">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="tergal_id" class="mb-1">Tergal</label>
                            <select id="tergal_id" name="detalle[tergal_id]" class="form-control select2" required
                                oninvalid="this.setCustomValidity('Por favor selecciona un tergal')"
                                oninput="this.setCustomValidity('')">
                                <option value="">Seleccione un tergal</option>
                                @foreach($tergales as $tergal)
                                    <option value="{{ $tergal->id }}"
                                        {{ old('detalle.tergal_id', $detalleCotizacion->tergal_id ?? '') == $tergal->id ? 'selected' : '' }}>
                                        {{ $tergal->nombre }} - {{ $tergal->campo1 ?? '' }} - {{ $tergal->campo2 ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="ancho_tergal">Ancho tela tergal</label>
                            <input type="text" name="detalle[ancho_tergal]" id="ancho_tergal" class="form-control"
                                value="{{ old('detalle.ancho_tergal', $detalleCotizacion->ancho_tergal ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="ancho_tergal_real">Ancho</label>
                            <input type="text" name="detalle[ancho_tergal_real]" id="ancho_tergal_real" class="form-control"
                                value="{{ old('detalle.ancho_tergal_real', $detalleCotizacion->ancho_tergal_real ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="largo_tergal">Largo</label>
                            <input type="text" name="detalle[largo_tergal]" id="largo_tergal" class="form-control"
                                value="{{ old('detalle.largo_tergal', $detalleCotizacion->largo_tergal ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="no_lienzos_tergal">No. Lienzos</label>
                            <input type="number" name="detalle[no_lienzos_tergal]" id="no_lienzos_tergal" class="form-control"
                                value="{{ old('detalle.no_lienzos_tergal', $detalleCotizacion->no_lienzos_tergal ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="no_lienzos_redondeado_tergal">No. Lienzos Redondeados</label>
                            <input type="number" name="detalle[no_lienzos_redondeado_tergal]" id="no_lienzos_redondeado_tergal" class="form-control"
                                value="{{ old('detalle.no_lienzos_redondeado_tergal', $detalleCotizacion->no_lienzos_redondeado_tergal ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="valor_bastilla_tergal">Bastilla</label>
                            <input type="number" id="valor_bastilla_tergal" name="detalle[valor_bastilla_tergal]" class="form-control"
                                value="{{ old('detalle.valor_bastilla_tergal', $detalleCotizacion->bastilla_tergal ?? '') }}"
                                placeholder="Ej. 1.10" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Sección de Forro -->
            @if(isset($cotizacion) && $cotizacion->lleva_forro)
            <div class="card mt-4">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Detalle de Forro</h4>
                </div>
                <div class="card-body pt-2">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="forro_id" class="mb-1">Forro</label>
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
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="ancho_forro">Ancho tela forro</label>
                            <input type="text" name="detalle[ancho_forro]" id="ancho_forro" class="form-control"
                                value="{{ old('detalle.ancho_forro', $detalleCotizacion->ancho_forro ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="ancho_forro_real">Ancho</label>
                            <input type="text" name="detalle[ancho_forro_real]" id="ancho_forro_real" class="form-control"
                                value="{{ old('detalle.ancho_forro_real', $detalleCotizacion->ancho_forro_real ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="largo_forro">Largo</label>
                            <input type="text" name="detalle[largo_forro]" id="largo_forro" class="form-control"
                                value="{{ old('detalle.largo_forro', $detalleCotizacion->largo_forro ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="no_lienzos_forro">No. Lienzos</label>
                            <input type="number" name="detalle[no_lienzos_forro]" id="no_lienzos_forro" class="form-control"
                                value="{{ old('detalle.no_lienzas_forro', $detalleCotizacion->no_lienzos_forro ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="no_lienzos_redondeado_forro">No. Lienzos Redondeados</label>
                            <input type="number" name="detalle[no_lienzos_redondeado_forro]" id="no_lienzos_redondeado_forro" class="form-control"
                                value="{{ old('detalle.no_lienzos_redondeado_forro', $detalleCotizacion->no_lienzos_redondeado_forro ?? '') }}">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="valor_bastilla_forro">Bastilla</label>
                            <input type="number" id="valor_bastilla_forro" name="detalle[valor_bastilla_forro]" class="form-control"
                                value="{{ old('detalle.valor_bastilla_forro', $detalleCotizacion->bastilla_forro ?? '') }}"
                                placeholder="Ej. 1.10" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tabla Totales Tela, Tergal y Forro -->
            <div class="card mt-4">
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
            <div class="card mt-4">
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
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Materiales Varios</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-success btn-sm" onclick="agregarOtroInsumo()">
                            <i class="fas fa-plus"></i> Añadir otro
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Materiales Varios</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="materiales-tbody">
                                <!-- Insumos fijos -->
                                @php
                                    $insumosFijosData = ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'];
                                    $insumosRelacionados = $cotizacion->insumos->keyBy('nombre');
                                @endphp
                                @foreach($insumosFijosData as $nombreInsumo)
                                    @php
                                        $insumoFijo = $insumosFijos->get($nombreInsumo);
                                        $insumoRelacionado = $insumosRelacionados->get($nombreInsumo);
                                        $cantidad = old('detalle.' . strtolower($nombreInsumo) . '_cantidad', $insumoRelacionado ? $insumoRelacionado->pivot->cantidad : '');
                                        $precio = $insumoFijo ? $insumoFijo->precio_publico : '';
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $nombreInsumo }}
                                            <input type="hidden" name="detalle[{{ strtolower($nombreInsumo) }}_id]" value="{{ $insumoFijo->id ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="number" name="detalle[{{ strtolower($nombreInsumo) }}_cantidad]" class="form-control" value="{{ $cantidad }}" autocomplete="off">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" value="{{ $precio }}" step="0.01" readonly>
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach

                                <!-- Otros insumos existentes -->
                                @php
                                    $otrosInsumosExistentes = $cotizacion->insumos->filter(function($insumo) use ($insumosFijosData) {
                                        return !in_array($insumo->nombre, $insumosFijosData);
                                    });
                                    $contador = 0;
                                @endphp
                                @foreach($otrosInsumosExistentes as $insumoExistente)
                                    @php $contador++; @endphp
                                    <tr class="otro-insumo-row">
                                        <td>
                                            <select name="detalle[otros{{ $contador }}_nombre]" id="otros{{ $contador }}_nombre" class="form-control">
                                                <option value="">Seleccionar insumo...</option>
                                                @foreach($insumos as $insumo)
                                                <option value="{{ $insumo->id }}" {{ $insumoExistente->id == $insumo->id ? 'selected' : '' }}>
                                                    {{ $insumo->nombre }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="detalle[otros{{ $contador }}_cantidad]" class="form-control" min="0" step="0.01" value="{{ $insumoExistente->pivot->cantidad }}">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="detalle[otros{{ $contador }}_precio]" class="form-control" min="0" step="0.01" value="{{ $insumoExistente->pivot->precio_unitario }}">
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarInsumo(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Costo Total Materiales:</strong></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[costo_total_materiales]" id="costo_total_materiales" class="form-control" value="{{ old('detalle.costo_total_materiales', $detalleCotizacion->costo_total_materiales ?? '') }}" readonly>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

<script>
let contadorOtrosInsumos = {{ $contador ?? 0 }};

function agregarOtroInsumo() {
    contadorOtrosInsumos++;
    const tbody = document.getElementById('materiales-tbody');
    const fila = document.createElement('tr');
    fila.className = 'otro-insumo-row';
    fila.innerHTML = `
        <td>
            <select name="detalle[otros${contadorOtrosInsumos}_nombre]" class="form-control">
                <option value="">Seleccionar insumo...</option>
                @foreach($insumos as $insumo)
                    <option value="{{ $insumo->id }}">{{ $insumo->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="detalle[otros${contadorOtrosInsumos}_cantidad]" class="form-control" min="0" step="0.01">
        </td>
        <td>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="detalle[otros${contadorOtrosInsumos}_precio]" class="form-control" min="0" step="0.01">
            </div>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarInsumo(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(fila);
}

function eliminarInsumo(button) {
    button.closest('.otro-insumo-row').remove();
    actualizarCostoTotalMateriales();
}

function actualizarCostoTotalMateriales() {
    let total = 0;
    document.querySelectorAll('#materiales-tbody tr').forEach(fila => {
        const cantidadInput = fila.querySelector('input[name*="_cantidad"]');
        const precioInput = fila.querySelector('input[name*="_precio"]');
        const cantidad = parseFloat(cantidadInput?.value) || 0;
        const precio = parseFloat(precioInput?.value) || 0;
        total += cantidad * precio;
    });
    document.getElementById('costo_total_materiales').value = total.toFixed(2);
}

// Actualiza el total cuando cambian cantidades o precios
document.addEventListener('input', function(e) {
    if (e.target && (e.target.name?.includes('_cantidad') || e.target.name?.includes('_precio'))) {
        actualizarCostoTotalMateriales();
    }
});
</script>

            <!-- Script para cálculos automáticos-->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const anchoInput = document.getElementById('ancho');
                    const anchoTelaInput = document.getElementById('ancho_tela');
                    const noLienzosInput = document.getElementById('no_lienzos');
                    const noLienzosRedondeadoInput = document.getElementById('no_lienzos_redondeado');

                    function calcularLienzos() {
                        const ancho = parseFloat(anchoInput.value) || 0;
                        const anchoTela = parseFloat(anchoTelaInput.value) || 0;

                        if (ancho > 0 && anchoTela > 0) {
                            const lienzos = ancho / anchoTela;
                            const lienzosRedondeado = Math.ceil(lienzos);

                            noLienzosInput.value = lienzos.toFixed(2);
                            noLienzosRedondeadoInput.value = lienzosRedondeado;
                        }
                    }

                    // Agregar event listeners para cálculo automático
                    anchoInput.addEventListener('input', calcularLienzos);
                    anchoTelaInput.addEventListener('input', calcularLienzas);
                });
            </script>
            
            <!-- Totales -->
            <div class="card">
                <div class="card-header">
                    <h4>Totales</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="total_lienzos">Total Lienzos</label>
                            <input type="number" name="totales[total_lienzos]" id="total_lienzos"
                                class="form-control" step="0.01" value="{{ $cotizacion->total_lienzos }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_m2_forro">Total M² Forro</label>
                            <input type="number" name="totales[total_m2_forro]" id="total_m2_forro"
                                class="form-control" step="0.01" value="{{ $cotizacion->total_m2_forro }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="total_m2_tela">Total M² Tela</label>
                            <input type="number" name="totales[total_m2_tela]" id="total_m2_tela"
                                class="form-control" step="0.01" value="{{ $cotizacion->total_m2_tela }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="total_m2_tergal">Total M² Tergal</label>
                            <input type="number" name="totales[total_m2_tergal]" id="total_m2_tergal"
                                class="form-control" step="0.01" value="{{ $cotizacion->total_m2_tergal }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="costo_cortina">Costo Cortina</label>
                            <input type="number" name="totales[costo_cortina]" id="costo_cortina"
                                class="form-control" step="0.01" value="{{ $cotizacion->costo_cortina }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="utilidad">Utilidad</label>
                            <input type="number" name="totales[utilidad]" id="utilidad"
                                class="form-control" step="0.01" value="{{ $cotizacion->utilidad }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="costo_decorador">Costo Decorador</label>
                            <input type="number" name="totales[costo_decorador]" id="costo_decorador"
                                class="form-control" step="0.01" value="{{ $cotizacion->costo_decorador }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="precio_publico">Precio Público</label>
                            <input type="number" name="totales[precio_publico]" id="precio_publico"
                                class="form-control" step="0.01" value="{{ $cotizacion->precio_publico }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Cotización
                </button>
                <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    let contadorOtrosInsumos = {
        {
            $otrosInsumosExistentes - > count()
        }
    };

    function agregarOtroInsumo() {
        contadorOtrosInsumos++;
        const container = document.getElementById('otros-insumos-container');

        const insumoDiv = document.createElement('div');
        insumoDiv.className = 'row mb-3 align-items-end otro-insumo-row';
        insumoDiv.innerHTML = `
        <div class="col-md-5">
            <label for="otros${contadorOtrosInsumos}_nombre">Insumo</label>
            <select name="detalle[otros${contadorOtrosInsumos}_nombre]" id="otros${contadorOtrosInsumos}_nombre" class="form-control">
                <option value="">Seleccionar insumo...</option>
                @foreach($insumos as $insumo)
                    <option value="{{ $insumo->id }}">{{ $insumo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="otros${contadorOtrosInsumos}_cantidad">Cantidad</label>
            <input type="number" name="detalle[otros${contadorOtrosInsumos}_cantidad]" 
                    id="otros${contadorOtrosInsumos}_cantidad" class="form-control" 
                    min="0" step="0.01">
        </div>
        <div class="col-md-3">
            <label for="otros${contadorOtrosInsumos}_precio">Precio Unitario</label>
            <input type="number" name="detalle[otros${contadorOtrosInsumos}_precio]" 
                    id="otros${contadorOtrosInsumos}_precio" class="form-control" 
                    min="0" step="0.01">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarInsumo(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

        container.appendChild(insumoDiv);
    }

    function eliminarInsumo(button) {
        button.closest('.otro-insumo-row').remove();
    }
</script>

@endsection