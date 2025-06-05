@extends('layouts.stisla')

@section('title', 'Editar Cotización')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Cotización</h1>
    </div>
    <div class="section-body">
        @php
            // Encuentra el insumo cortinero seleccionado (id_tipo_insumo = 6)
            $cortineroSeleccionado = $cotizacion->insumos->first(function($insumo) {
                return $insumo->id_tipo_insumo == 6;
            });
        @endphp
        <form method="POST" action="{{ route('admin.cotizaciones.update', $cotizacion->id) }}">
            @csrf
            @method('PUT')

            <!-- Información General -->
            <div class="row">
                <div class="col-md-4 mb-3">
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
                <div class="col-md-4 mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ $cotizacion->fecha }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="estatus">Estatus</label>
                    <select name="estatus" id="estatus" class="form-control" required>
                        <option value="solicitada" {{ $cotizacion->estatus == 'solicitada' ? 'selected' : '' }}>Solicitada</option>
                        <option value="aceptada" {{ $cotizacion->estatus == 'aceptada' ? 'selected' : '' }}>Aceptada</option>
                        <option value="rechazada" {{ $cotizacion->estatus == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    </select>
                </div>
            </div>

            {{-- Checkboxes --}}
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

            <!-- PHP para secciones Cortina, Tergal y forro -->
            @php
                function limpiarPrecio($valor) {
                    $valor = str_replace(['$', ' '], '', $valor);
                    $valor = str_replace(',', '.', $valor);
                    return floatval($valor);
                }
                $esAdmin = auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador';
            @endphp

            <!-- Sección de Cortina -->
            <div class="card mt-4" id="seccion-cortina" style="display: none;">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Detalle de Cortina</h4>
                </div>
                <div class="card-body pt-2">
                    <!-- Select Tela fuera de la tabla -->
                    <div class="mb-3">
                        <label for="tela_id" class="form-label">Tela</label>
                        <select id="tela_id" name="detalle[tela_id]" class="form-control select2"
                            oninvalid="this.setCustomValidity('Por favor selecciona una tela')"
                            oninput="this.setCustomValidity('')">
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
                                <option value="{{ $tela->id }}" data-precio="{{ $precio }}"
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
                                    <th>Ancho tela cortina</th>
                                    <th>Ancho</th>
                                    <th>Largo</th>
                                    <th>No. Lienzos</th>
                                    <th>No. Lienzos Redondeados</th>
                                    <th>Bastilla</th>
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
                                            value="{{ old('detalle.valor_bastilla', $detalleCotizacion->bastilla ?? '') }}" 
                                            placeholder="Ej. 1.10m" step="0.01" min="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Tergal -->
            <div class="card mt-4" id="seccion-tergal" style="display: none;">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Detalle de Tergal</h4>
                </div>
                <div class="card-body pt-2">
                    <!-- Select Tergal fuera de la tabla -->
                    <div class="mb-3">
                        <label for="tergal_id" class="form-label">Tergal</label>
                        <select id="tergal_id" name="detalle[tergal_id]" class="form-control select2"
                            oninvalid="this.setCustomValidity('Por favor selecciona un tergal')"
                            oninput="this.setCustomValidity('')">
                            <option value="">Seleccione un tergal</option>
                            @foreach($tergales as $tergal)
                                @php
                                if(limpiarPrecio($tergal->precio_publico) > 0) {
                                    $precio = limpiarPrecio($tergal->precio_publico);
                                } elseif(limpiarPrecio($tergal->campo6) > 0) {
                                    $precio = limpiarPrecio($tergal->campo6);
                                } elseif(limpiarPrecio($tergal->campo13) > 0) {
                                    $precio = limpiarPrecio($tergal->campo13);
                                } else {
                                    $precio = 50;
                                }
                                @endphp
                                <option value="{{ $tergal->id }}" data-precio="{{ $precio }}"
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
                                    <th>Ancho tela tergal</th>
                                    <th>Ancho</th>
                                    <th>Largo</th>
                                    <th>No. Lienzos</th>
                                    <th>No. Lienzos Redondeados</th>
                                    <th>Bastilla</th>
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
                                            value="{{ old('detalle.valor_bastilla_tergal', $detalleCotizacion->bastilla_tergal ?? '') }}"
                                            placeholder="Ej. 0.65m" step="0.01" min="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sección de Forro -->
            <div class="card mt-4" id="seccion-forro" style="display: none;">
                <div class="card-header pb-1">
                    <h4 class="mb-1">Detalle de Forro</h4>
                </div>
                <div class="card-body pt-2">
                    <!-- Select Forro fuera de la tabla -->
                    <div class="mb-3">
                        <label for="forro_id" class="form-label">Forro</label>
                        <select id="forro_id" name="detalle[forro_id]" class="form-control select2"
                            oninvalid="this.setCustomValidity('Por favor selecciona un forro')"
                            oninput="this.setCustomValidity('')">
                            <option value="">Seleccione un forro</option>
                            @foreach($forros as $forro)
                                @php
                                if(limpiarPrecio($forro->precio_publico) > 0) {
                                    $precio = limpiarPrecio($forro->precio_publico);
                                } elseif(limpiarPrecio($forro->campo6) > 0) {
                                    $precio = limpiarPrecio($forro->campo6);
                                } elseif(limpiarPrecio($forro->campo13) > 0) {
                                    $precio = limpiarPrecio($forro->campo13);
                                } else {
                                    $precio = 30;
                                }
                                @endphp
                                <option value="{{ $forro->id }}" data-precio="{{ $precio }}"
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
                                    <th>Ancho tela forro</th>
                                    <th>Ancho</th>
                                    <th>Largo</th>
                                    <th>No. Lienzos</th>
                                    <th>No. Lienzos Redondeados</th>
                                    <th>Bastilla</th>
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
                                            value="{{ old('detalle.no_lienzos_forro', $detalleCotizacion->no_lienzas_forro ?? '') }}" step="0.01" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[no_lienzos_redondeado_forro]" id="no_lienzos_redondeado_forro" class="form-control"
                                            value="{{ old('detalle.no_lienzos_redondeado_forro', $detalleCotizacion->no_lienzos_redondeado_forro ?? '') }}" step="0.01" min="0"> 
                                    </td>
                                    <td>
                                        <input type="number" id="valor_bastilla_forro" name="detalle[valor_bastilla_forro]" class="form-control"
                                            value="{{ old('detalle.valor_bastilla_forro', $detalleCotizacion->bastilla_forro ?? '') }}"
                                            placeholder="Ej. 0.40m" step="0.01" min="0">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabla Totales Tela, Tergal y Forro -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Totales Tela, Tergal y Forro</h4>
                    <button type="button" class="btn btn-primary btn-sm" onclick="calcularTotales()">Calcular Totales</button>
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
                                            value="{{ old('detalle.total_tela', $detalleCotizacion->total_tela ?? '') }}" readonly>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[precio_m2_tela_final]" id="precio_m2_tela_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.precio_m2_tela_final', $detalleCotizacion->precio_m2_tela_final ?? $detalleCotizacion->precio_m2_tela ?? '') }}" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="detalle[descripcion_tela]" class="form-control" placeholder="Cortina"
                                            value="{{ old('detalle.descripcion_tela', $detalleCotizacion->descripcion_tela ?? 'Cortina') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_tela_final]" id="total_tela_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_tela_final', $detalleCotizacion->total_tela_final ?? '') }}" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Tergal -->
                                <tr>
                                    <td>
                                        <input type="number" name="detalle[total_tergal]" id="total_tergal" class="form-control" step="0.01"
                                            value="{{ old('detalle.total_tergal', $detalleCotizacion->total_tergal ?? '') }}" readonly>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[precio_m2_tergal_final]" id="precio_m2_tergal_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.precio_m2_tergal_final', $detalleCotizacion->precio_m2_tergal_final ?? $detalleCotizacion->precio_m2_tergal ?? '') }}" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="detalle[descripcion_tergal]" class="form-control" placeholder="Tergal"
                                            value="{{ old('detalle.descripcion_tergal', $detalleCotizacion->descripcion_tergal ?? 'Tergal') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_tergal_final]" id="total_tergal_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_tergal_final', $detalleCotizacion->total_tergal_final ?? '') }}" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Fila Forro -->
                                <tr>
                                    <td>
                                        <input type="number" id="total_forro" name="detalle[total_forro]" class="form-control" step="0.01"
                                            value="{{ old('detalle.total_forro', $detalleCotizacion->total_forro ?? '') }}" readonly>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[precio_m2_forro_final]" id="precio_m2_forro_final" class="form-control" step="0.01"
                                                value="{{ old('detalle.precio_m2_forro_final', $detalleCotizacion->precio_m2_forro_final ?? $detalleCotizacion->precio_m2_forro ?? '') }}" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="detalle[descripcion_forro]" class="form-control" placeholder="Forro"
                                            value="{{ old('detalle.descripcion_forro', $detalleCotizacion->descripcion_forro ?? 'Forro') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_final_forro]" id="total_final_forro" class="form-control" step="0.01"
                                                value="{{ old('detalle.total_final_forro', $detalleCotizacion->total_final_forro ?? '') }}" readonly>
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
                                                value="{{ old('detalle.costo_total_tela_tergal_forro', $detalleCotizacion->costo_total_tela_tergal_forro ?? '') }}" readonly>
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
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 180px;">Materiales Varios</th>
                                    <th style="min-width: 120px;">Cantidad</th>
                                    <th style="min-width: 150px;">Precio Unitario</th>
                                    <th style="min-width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="materiales-tbody">
                                <!-- Ojillos -->
                                <tr>
                                    <td>
                                        Ojillos
                                        <input type="hidden" name="detalle[ojillos_id]" value="{{ $insumosFijos['Ojillos']->id ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[ojillos_cantidad]" class="form-control" min="0" step="0.01"
                                            value="{{ old('detalle.ojillos_cantidad', $cotizacion->insumos->where('nombre', 'Ojillos')->first()->pivot->cantidad ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{ $insumosFijos['Ojillos']->precio_publico ?? '' }}" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <!-- Cortinero -->
                                <tr>
                                    <td>
                                        Cortinero
                                        <select name="detalle[cortinero_id]" id="cortinero_id" class="form-select">
                                            <option value="">Seleccione tipo de cortinero</option>
                                            @foreach($cortineros as $cortinero)
                                            <option value="{{ $cortinero->id }}" data-precio="{{ $cortinero->precio_publico }}"
                                                {{ old('detalle.cortinero_id', $cortineroSeleccionado->id ?? '') == $cortinero->id ? 'selected' : '' }}>
                                                {{ $cortinero->nombre }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[cortinero_cantidad]" id="cortinero_cantidad" class="form-control" min="0" step="0.01"
                                            value="{{ old('detalle.cortinero_cantidad', $cortineroSeleccionado->pivot->cantidad ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_precio" name="detalle[cortinero_precio]" class="form-control" step="0.01" readonly
                                                value="{{ old('detalle.cortinero_precio', $cortineroSeleccionado->pivot->precio_unitario ?? '') }}">
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const cortineroSelect = document.getElementById('cortinero_id');
                                                const cortineroPrecio = document.getElementById('cortinero_precio');
                                                if (cortineroSelect && cortineroPrecio) {
                                                    function setPrecioCortinero() {
                                                        const selected = cortineroSelect.options[cortineroSelect.selectedIndex];
                                                        cortineroPrecio.value = selected && selected.dataset.precio ? selected.dataset.precio : '';
                                                        if (typeof actualizarCostoTotalMateriales === 'function') {
                                                            actualizarCostoTotalMateriales();
                                                        }
                                                    }
                                                    cortineroSelect.addEventListener('change', setPrecioCortinero);
                                                    // Al cargar, actualiza el precio según el seleccionado
                                                    setPrecioCortinero();
                                                }
                                            });
                                        </script>
                                    </td>
                                    <td></td>
                                </tr>
                                <!-- Puntas -->
                                <tr>
                                    <td>
                                        Puntas
                                        <input type="hidden" name="detalle[puntas_id]" value="{{ $insumosFijos['Puntas']->id ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[puntas_cantidad]" class="form-control" min="0" step="0.01"
                                            value="{{ old('detalle.puntas_cantidad', $cotizacion->insumos->where('nombre', 'Puntas')->first()->pivot->cantidad ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{ $insumosFijos['Puntas']->precio_publico ?? '' }}" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <!-- Mensulas -->
                                <tr>
                                    <td>
                                        Mensulas
                                        <input type="hidden" name="detalle[mensulas_id]" value="{{ $insumosFijos['Mensulas']->id ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[mensulas_cantidad]" class="form-control" min="0" step="0.01"
                                            value="{{ old('detalle.mensulas_cantidad', $cotizacion->insumos->where('nombre', 'Mensulas')->first()->pivot->cantidad ?? '') }}">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{ $insumosFijos['Mensulas']->precio_publico ?? '' }}" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <!-- Otros insumos existentes -->
                                @php
                                    $insumosFijosData = ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'];
                                    // Excluir también los insumos de tipo cortinero (id_tipo_insumo == 6)
                                    $otrosInsumosExistentes = $cotizacion->insumos->filter(function($insumo) use ($insumosFijosData) {
                                        return !in_array($insumo->nombre, $insumosFijosData) && $insumo->id_tipo_insumo != 6;
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
                                <tr id="row-boton-otro-insumo">
                                    <td colspan="4" class="text-start">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="agregarOtroInsumo()">Añadir otro</button>
                                    </td>
                                </tr>
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
                                            <input type="number" class="form-control" id="total_lienzos" name="totales[total_lienzos]" value="{{ old('totales.total_lienzos', $detalleCotizacion->total_lienzos ?? $cotizacion->total_lienzos) }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Forro</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_forro" name="totales[total_m2_forro]" value="{{ old('totales.total_m2_forro', $detalleCotizacion->total_m2_forro ?? $cotizacion->total_m2_forro) }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Tela</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_tela" name="totales[total_m2_tela]" value="{{ old('totales.total_m2_tela', $detalleCotizacion->total_m2_tela ?? $cotizacion->total_m2_tela) }}" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Tergal</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_tergal" name="totales[total_m2_tergal]" value="{{ old('totales.total_m2_tergal', $detalleCotizacion->total_m2_tergal ?? $cotizacion->total_m2_tergal) }}" readonly>
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
                                                <input type="number" class="form-control" id="costo_cortina" name="totales[costo_cortina]" value="{{ $cotizacion->costo_cortina }}" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        @if($esAdmin)
                                            <td><strong>Utilidad</strong></td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" id="utilidad" name="totales[utilidad]" value="{{ $cotizacion->utilidad }}" readonly>
                                                </div>
                                            </td>
                                        @else
                                            <td colspan="2">
                                                <input type="hidden" id="utilidad" name="totales[utilidad]" value="{{ $cotizacion->utilidad }}">
                                            </td>
                                        @endif
                                    </tr>
                                    <tr>
                                        @if($esAdmin)
                                            <td><strong>Costo Decorador</strong></td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" id="decorador_porcentaje" class="form-control text-end" value="15" min="0" max="100" step="0.01" style="max-width: 100px;">
                                                    <span class="input-group-text">%</span>
                                                    <span class="input-group-text" style="margin-left: 0.5rem;">$</span>
                                                    <input type="number" class="form-control" id="costo_decorador" name="totales[costo_decorador]" value="{{ $cotizacion->costo_decorador }}" readonly>
                                                </div>
                                            </td>
                                        @else
                                            <td colspan="2">
                                                <input type="hidden" id="costo_decorador" name="totales[costo_decorador]" value="{{ $cotizacion->costo_decorador }}">
                                            </td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td><strong>Precio Público</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="precio_publico" name="totales[precio_publico]" value="{{ $cotizacion->precio_publico }}" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones final del formulario --}}
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
@endsection

@section('scripts')
<script>
    // Listener de los checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const cortinaCheck = document.getElementById('cortinaCheck');
        const tergalCheck = document.getElementById('tergalCheck');
        const forroCheck = document.getElementById('forroCheck');
        const seccionCortina = document.getElementById('seccion-cortina');
        const seccionTergal = document.getElementById('seccion-tergal');
        const seccionForro = document.getElementById('seccion-forro');
        function mostrarOcultarSecciones() {
            if (seccionCortina) seccionCortina.style.display = cortinaCheck.checked ? '' : 'none';
            if (seccionTergal) seccionTergal.style.display = tergalCheck.checked ? '' : 'none';
            if (seccionForro) seccionForro.style.display = forroCheck.checked ? '' : 'none';
        }
        cortinaCheck.addEventListener('change', mostrarOcultarSecciones);
        tergalCheck.addEventListener('change', mostrarOcultarSecciones);
        forroCheck.addEventListener('change', mostrarOcultarSecciones);
        // Ejecutar al cargar
        mostrarOcultarSecciones();
    });

    // Calcular No. Lienzos Cortina
    document.addEventListener('DOMContentLoaded', function() {
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
        }
        const anchoInput = document.getElementById('ancho');
        const anchoTelaInput = document.getElementById('ancho_tela');
        if (anchoInput && anchoTelaInput) {
            anchoInput.addEventListener('input', calcularLienzos);
            anchoTelaInput.addEventListener('input', calcularLienzos);
        }
        calcularLienzos();
    });

    // Calcular No. Lienzos Tergal
    document.addEventListener('DOMContentLoaded', function() {
        function calcularLienzosTergal() {
            const anchoTergal = parseFloat(document.getElementById('ancho_tergal_real')?.value || 0);
            const anchoTelaTergal = parseFloat(document.getElementById('ancho_tergal')?.value || 0);

            if (anchoTergal > 0 && anchoTelaTergal > 0) {
                const lienzos = (anchoTergal * 2.5) / anchoTelaTergal;
                const lienzosRedondeado = Math.ceil(lienzos);

                document.getElementById('no_lienzos_tergal').value = lienzos.toFixed(2);
                document.getElementById('no_lienzos_redondeado_tergal').value = lienzosRedondeado;
            } else {
                document.getElementById('no_lienzos_tergal').value = '';
                document.getElementById('no_lienzos_redondeado_tergal').value = '';
            }
        }
        const anchoTergalInput = document.getElementById('ancho_tergal_real');
        const anchoTelaTergalInput = document.getElementById('ancho_tergal');
        if (anchoTergalInput && anchoTelaTergalInput) {
            anchoTergalInput.addEventListener('input', calcularLienzosTergal);
            anchoTelaTergalInput.addEventListener('input', calcularLienzosTergal);
        }
        calcularLienzosTergal();
    });

    // Calcular No. Lienzos Forro
    document.addEventListener('DOMContentLoaded', function() {
        function calcularLienzosForro() {
            const anchoForro = parseFloat(document.getElementById('ancho_forro_real')?.value || 0);
            const anchoTelaForro = parseFloat(document.getElementById('ancho_forro')?.value || 0);

            if (anchoForro > 0 && anchoTelaForro > 0) {
                const lienzos = (anchoForro * 2.5) / anchoTelaForro;
                const lienzosRedondeado = Math.ceil(lienzos);

                document.getElementById('no_lienzos_forro').value = lienzos.toFixed(2);
                document.getElementById('no_lienzos_redondeado_forro').value = lienzosRedondeado;
            } else {
                document.getElementById('no_lienzos_forro').value = '';
                document.getElementById('no_lienzos_redondeado_forro').value = '';
            }
        }
        const anchoForroInput = document.getElementById('ancho_forro_real');
        const anchoTelaForroInput = document.getElementById('ancho_forro');
        if (anchoForroInput && anchoTelaForroInput) {
            anchoForroInput.addEventListener('input', calcularLienzosForro);
            anchoTelaForroInput.addEventListener('input', calcularLienzosForro);
        }
        calcularLienzosForro();
    });
    
    // Copiar valores de Cortina a Tergal 
    function copiarCortinaATergal() {
        const anchoCortina = document.getElementById('ancho');
        const largoCortina = document.getElementById('largo');
        const anchoTelaCortina = document.getElementById('ancho_tela');
        const anchoTergal = document.getElementById('ancho_tergal_real');
        const largoTergal = document.getElementById('largo_tergal');
        const anchoTelaTergal = document.getElementById('ancho_tergal');

        if (anchoCortina && anchoTergal) anchoTergal.value = anchoCortina.value;
        if (largoCortina && largoTergal) largoTergal.value = largoCortina.value;
        if (anchoTelaCortina && anchoTelaTergal) anchoTelaTergal.value = anchoTelaCortina.value;

        // Disparar eventos para recalcular
        if (anchoTergal) anchoTergal.dispatchEvent(new Event('input', { bubbles: true }));
        if (anchoTelaTergal) anchoTelaTergal.dispatchEvent(new Event('input', { bubbles: true }));
    }

    // Copiar valores de Cortina o Tergal a Forro
    function copiarCortinaOTergalAForro() {
        const cortinaCheck = document.getElementById('cortinaCheck');
        const anchoCortina = document.getElementById('ancho');
        const largoCortina = document.getElementById('largo');
        const anchoTelaCortina = document.getElementById('ancho_tela');
        const noLienzosCortina = document.getElementById('no_lienzos');
        const noLienzosRedondeadoCortina = document.getElementById('no_lienzos_redondeado');

        const anchoTergal = document.getElementById('ancho_tergal_real');
        const largoTergal = document.getElementById('largo_tergal');
        const anchoTelaTergal = document.getElementById('ancho_tergal');
        const noLienzosTergal = document.getElementById('no_lienzos_tergal');
        const noLienzosRedondeadoTergal = document.getElementById('no_lienzos_redondeado_tergal');

        const anchoForro = document.getElementById('ancho_forro_real');
        const largoForro = document.getElementById('largo_forro');
        const anchoTelaForro = document.getElementById('ancho_forro');
        const noLienzosForro = document.getElementById('no_lienzos_forro');
        const noLienzosRedondeadoForro = document.getElementById('no_lienzos_redondeado_forro');

        if (cortinaCheck && cortinaCheck.checked) {
            if (anchoCortina && anchoForro) anchoForro.value = anchoCortina.value;
            if (largoCortina && largoForro) largoForro.value = largoCortina.value;
            if (anchoTelaCortina && anchoTelaForro) anchoTelaForro.value = anchoTelaCortina.value;
            if (noLienzosCortina && noLienzosForro) noLienzosForro.value = noLienzosCortina.value;
            if (noLienzosRedondeadoCortina && noLienzosRedondeadoForro) noLienzosRedondeadoForro.value = noLienzosRedondeadoCortina.value;
        } else {
            if (anchoTergal && anchoForro) anchoForro.value = anchoTergal.value;
            if (largoTergal && largoForro) largoForro.value = largoTergal.value;
            if (anchoTelaTergal && anchoTelaForro) anchoTelaForro.value = anchoTelaTergal.value;
            if (noLienzosTergal && noLienzosForro) noLienzosForro.value = noLienzosTergal.value;
            if (noLienzosRedondeadoTergal && noLienzosRedondeadoForro) noLienzosRedondeadoForro.value = noLienzosRedondeadoTergal.value;
        }

        // Disparar eventos para recalcular
        if (anchoForro) anchoForro.dispatchEvent(new Event('input', { bubbles: true }));
        if (anchoTelaForro) anchoTelaForro.dispatchEvent(new Event('input', { bubbles: true }));
        if (noLienzosForro) noLienzosForro.dispatchEvent(new Event('input', { bubbles: true }));
        if (noLienzosRedondeadoForro) noLienzosRedondeadoForro.dispatchEvent(new Event('input', { bubbles: true }));
    }

    // Listeners para copiar valores cuando se muestra la sección o cambian los campos base
    document.addEventListener('DOMContentLoaded', function() {
        const cortinaCheck = document.getElementById('cortinaCheck');
        const tergalCheck = document.getElementById('tergalCheck');
        const forroCheck = document.getElementById('forroCheck');

        if (cortinaCheck) {
            cortinaCheck.addEventListener('change', function() {
                if (tergalCheck && tergalCheck.checked) copiarCortinaATergal();
                if (forroCheck && forroCheck.checked) copiarCortinaOTergalAForro();
            });
        }

        if (tergalCheck) {
            tergalCheck.addEventListener('change', function() {
                if (tergalCheck.checked) copiarCortinaATergal();
            });
        }

        if (forroCheck) {
            forroCheck.addEventListener('change', function() {
                if (forroCheck.checked) copiarCortinaOTergalAForro();
            });
        }

        ['ancho', 'largo', 'ancho_tela'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function() {
                    if (tergalCheck && tergalCheck.checked) copiarCortinaATergal();
                    if (forroCheck && forroCheck.checked) copiarCortinaOTergalAForro();
                });
            }
        });

        ['ancho_tergal_real', 'largo_tergal', 'ancho_tergal'].forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.addEventListener('input', function() {
                    const cortinaCheck = document.getElementById('cortinaCheck');
                    const forroCheck = document.getElementById('forroCheck');
                    if (forroCheck && forroCheck.checked && (!cortinaCheck || !cortinaCheck.checked)) {
                        copiarCortinaOTergalAForro();
                    }
                });
            }
        });
    });

    // Actualiza el largo según la bastilla, propagando el nuevo largo a tergal y forro si corresponde
    function actualizarLargoConBastilla(idLargo, idBastilla) {
        const largoInput = document.getElementById(idLargo);
        const bastillaInput = document.getElementById(idBastilla);

        if (!largoInput || !bastillaInput) return;

        // Inicializar el valor base (sin bastilla)
        if (!largoInput.dataset.original) {
            const largoActual = parseFloat(largoInput.value) || 0;
            const bastillaActual = parseFloat(bastillaInput.value) || 0;
            largoInput.dataset.original = (largoActual - bastillaActual).toFixed(2);
        }

        // Cuando cambia la bastilla
        bastillaInput.addEventListener('input', function() {
            let largoBase = parseFloat(largoInput.dataset.original) || 0;
            let bastilla = parseFloat(bastillaInput.value) || 0;

            if (largoBase > 0 && bastilla >= 0) {
                largoInput.value = (largoBase + bastilla).toFixed(2);
            } else if (largoBase > 0) {
                largoInput.value = largoBase.toFixed(2);
            }

            // Propagar el nuevo largo a tergal y forro si corresponde
            if (idLargo === 'largo') {
                // Si existe el campo de tergal y está visible
                const largoTergal = document.getElementById('largo_tergal');
                const bastillaTergal = document.getElementById('valor_bastilla_tergal');
                if (largoTergal && bastillaTergal && document.getElementById('tergalCheck')?.checked) {
                    // El valor base de tergal es el largo de cortina con bastilla de cortina
                    largoTergal.dataset.original = largoInput.value;
                    // Suma la bastilla de tergal
                    const bastillaT = parseFloat(bastillaTergal.value) || 0;
                    largoTergal.value = (parseFloat(largoInput.value) + bastillaT).toFixed(2);
                }
                // Igual para forro
                const largoForro = document.getElementById('largo_forro');
                const bastillaForro = document.getElementById('valor_bastilla_forro');
                if (largoForro && bastillaForro && document.getElementById('forroCheck')?.checked) {
                    largoForro.dataset.original = largoInput.value;
                    const bastillaF = parseFloat(bastillaForro.value) || 0;
                    largoForro.value = (parseFloat(largoInput.value) + bastillaF).toFixed(2);
                }
            }
            if (idLargo === 'largo_tergal') {
                // Si el largo de forro depende de tergal (y no de cortina)
                const cortinaCheck = document.getElementById('cortinaCheck');
                const largoForro = document.getElementById('largo_forro');
                const bastillaForro = document.getElementById('valor_bastilla_forro');
                if (largoForro && bastillaForro && document.getElementById('forroCheck')?.checked && (!cortinaCheck || !cortinaCheck.checked)) {
                    largoForro.dataset.original = largoInput.value;
                    const bastillaF = parseFloat(bastillaForro.value) || 0;
                    largoForro.value = (parseFloat(largoInput.value) + bastillaF).toFixed(2);
                }
            }
        });

        // Cuando cambia el largo manualmente, actualiza el valor base
        largoInput.addEventListener('input', function() {
            const bastilla = parseFloat(bastillaInput.value) || 0;
            const largoActual = parseFloat(largoInput.value) || 0;
            largoInput.dataset.original = (largoActual - bastilla).toFixed(2);
        });
    }

    // Listeners para aplicar bastilla de tergal y forro en cuanto se escriban
    document.addEventListener('DOMContentLoaded', function() {
        // Tergal
        const bastillaTergal = document.getElementById('valor_bastilla_tergal');
        const largoTergal = document.getElementById('largo_tergal');
        const tergalCheck = document.getElementById('tergalCheck');
        const largoCortina = document.getElementById('largo');
        if (bastillaTergal && largoTergal && tergalCheck && largoCortina) {
            bastillaTergal.addEventListener('input', function() {
                if (tergalCheck.checked) {
                    // El valor base de tergal es el largo de cortina con bastilla de cortina
                    largoTergal.dataset.original = largoCortina.value;
                    const bastillaT = parseFloat(bastillaTergal.value) || 0;
                    largoTergal.value = (parseFloat(largoCortina.value) + bastillaT).toFixed(2);
                }
            });
            tergalCheck.addEventListener('change', function() {
                if (tergalCheck.checked) {
                    largoTergal.dataset.original = largoCortina.value;
                    const bastillaT = parseFloat(bastillaTergal.value) || 0;
                    largoTergal.value = (parseFloat(largoCortina.value) + bastillaT).toFixed(2);
                }
            });
        }
        // Forro
        const bastillaForro = document.getElementById('valor_bastilla_forro');
        const largoForro = document.getElementById('largo_forro');
        const forroCheck = document.getElementById('forroCheck');
        if (bastillaForro && largoForro && forroCheck) {
            bastillaForro.addEventListener('input', function() {
                let base = 0;
                const cortinaCheck = document.getElementById('cortinaCheck');
                if (cortinaCheck && cortinaCheck.checked && largoCortina) {
                    base = parseFloat(largoCortina.value) || 0;
                } else {
                    const largoTergal = document.getElementById('largo_tergal');
                    if (largoTergal) base = parseFloat(largoTergal.value) || 0;
                }
                if (forroCheck.checked) {
                    largoForro.dataset.original = base;
                    const bastillaF = parseFloat(bastillaForro.value) || 0;
                    largoForro.value = (base + bastillaF).toFixed(2);
                }
            });
            forroCheck.addEventListener('change', function() {
                let base = 0;
                const cortinaCheck = document.getElementById('cortinaCheck');
                if (cortinaCheck && cortinaCheck.checked && largoCortina) {
                    base = parseFloat(largoCortina.value) || 0;
                } else {
                    const largoTergal = document.getElementById('largo_tergal');
                    if (largoTergal) base = parseFloat(largoTergal.value) || 0;
                }
                if (forroCheck.checked) {
                    largoForro.dataset.original = base;
                    const bastillaF = parseFloat(bastillaForro.value) || 0;
                    largoForro.value = (base + bastillaF).toFixed(2);
                }
            });
        }
    });

    // Listeners para la bastilla y largo
    document.addEventListener('DOMContentLoaded', function() {
        actualizarLargoConBastilla('largo', 'valor_bastilla');
        actualizarLargoConBastilla('largo_tergal', 'valor_bastilla_tergal');
        actualizarLargoConBastilla('largo_forro', 'valor_bastilla_forro');
    });

    // Calcular y actualizar totales de Tela, Tergal y Forro 
    function calcularTotalesTelaTergalForro() {
        const cortinaCheck = document.getElementById('cortinaCheck')?.checked || false;
        const tergalCheck = document.getElementById('tergalCheck')?.checked || false;
        const forroCheck = document.getElementById('forroCheck')?.checked || false;
        
        let totalTela = 0, totalTergal = 0, totalForro = 0;
        let totalTelaFinal = 0, totalTergalFinal = 0, totalForroFinal = 0;
        let precioTela = 0, precioTergal = 0, precioForro = 0;
        
        const noLienzosCortina = parseFloat(document.getElementById('no_lienzos_redondeado')?.value) || 0;
        const largoCortina = parseFloat(document.getElementById('largo')?.value) || 0;
        precioTela = parseFloat(document.getElementById('tela_id')?.selectedOptions[0]?.dataset.precio) || 0;
        
        totalTela = noLienzosCortina * largoCortina;
        totalTelaFinal = totalTela * precioTela;
        
        const noLienzosTergal = parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value) || 0;
        const largoTergal = parseFloat(document.getElementById('largo_tergal')?.value) || 0;
        precioTergal = parseFloat(document.getElementById('tergal_id')?.selectedOptions[0]?.dataset.precio) || 0;
        
        totalTergal = noLienzosTergal * largoTergal;
        totalTergalFinal = totalTergal * precioTergal;
        
        const noLienzosForro = parseFloat(document.getElementById('no_lienzos_redondeado_forro')?.value) || 0;
        const largoForro = parseFloat(document.getElementById('largo_forro')?.value) || 0;
        precioForro = parseFloat(document.getElementById('forro_id')?.selectedOptions[0]?.dataset.precio) || 0;
        
        totalForro = noLienzosForro * largoForro;
        totalForroFinal = totalForro * precioForro;
        
        function actualizarCampo(id, valor, checkboxActivo = true) {
            const input = document.getElementById(id);
            if (input) {
                input.value = (checkboxActivo && valor > 0) ? valor.toFixed(2) : '';
            }
        }
        
        actualizarCampo('total_tela', totalTela, cortinaCheck);
        actualizarCampo('precio_m2_tela_final', precioTela, cortinaCheck);
        actualizarCampo('total_tela_final', totalTelaFinal, cortinaCheck);
        
        actualizarCampo('total_tergal', totalTergal, tergalCheck);
        actualizarCampo('precio_m2_tergal_final', precioTergal, tergalCheck);
        actualizarCampo('total_tergal_final', totalTergalFinal, tergalCheck);
        
        actualizarCampo('total_forro', totalForro, forroCheck);
        actualizarCampo('precio_m2_forro_final', precioForro, forroCheck);
        actualizarCampo('total_final_forro', totalForroFinal, forroCheck);
        
        const costoTotal = (cortinaCheck ? totalTelaFinal : 0) + 
                            (tergalCheck ? totalTergalFinal : 0) + 
                            (forroCheck ? totalForroFinal : 0);
        
        actualizarCampo('costo_total_tela_tergal_forro', costoTotal, (cortinaCheck || tergalCheck || forroCheck));
    }

    // Función para actualizar precio individual
    function actualizarPrecioIndividual(selectElement, precioInputId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const precioInput = document.getElementById(precioInputId);
        
        if (selectedOption && selectedOption.value && precioInput) {
            const precio = selectedOption.getAttribute('data-precio');
            if (precio) {
                precioInput.value = parseFloat(precio).toFixed(2);
                precioInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    }

    // Configuración principal al cargar el DOM
    document.addEventListener('DOMContentLoaded', function() {
        const selectConfigs = [
            {
                selectId: 'tela_id',
                precioInputId: 'precio_m2_tela_final'
            },
            {
                selectId: 'tergal_id',
                precioInputId: 'precio_m2_tergal_final'
            },
            {
                selectId: 'forro_id',
                precioInputId: 'precio_m2_forro_final'
            }
        ];

        function agregarListenersSelect(config) {
            const selectElement = document.getElementById(config.selectId);
            if (!selectElement) return;

            selectElement.addEventListener('change', function() {
                actualizarPrecioIndividual(this, config.precioInputId);
                calcularTotalesTelaTergalForro();
            });

            $(selectElement).on('select2:select', function(e) {
                actualizarPrecioIndividual(this, config.precioInputId);
                calcularTotalesTelaTergalForro();
            });

            $(selectElement).on('change.select2', function(e) {
                actualizarPrecioIndividual(this, config.precioInputId);
                calcularTotalesTelaTergalForro();
            });
        }

        selectConfigs.forEach(agregarListenersSelect);

        // Listeners para inputs que afectan los cálculos
        const inputsCalculos = [
            // Campos de cortina
            'no_lienzos_redondeado', 'largo', 'precio_m2_tela', 'precio_m2_tela_final',
            // Campos de tergal
            'no_lienzos_redondeado_tergal', 'largo_tergal', 'precio_m2_tergal', 'precio_m2_tergal_final',
            // Campos de forro
            'no_lienzos_redondeado_forro', 'largo_forro', 'precio_m2_forro', 'precio_m2_forro_final'
        ];

        inputsCalculos.forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                // Usar 'input' para cambios en tiempo real mientras se escribe
                elemento.addEventListener('input', function() {
                    calcularTotalesTelaTergalForro();
                });
                // También 'change' para cuando pierda el foco
                elemento.addEventListener('change', function() {
                    calcularTotalesTelaTergalForro();
                });
                // Agregar 'keyup' para asegurar que se dispare al escribir
                elemento.addEventListener('keyup', function() {
                    calcularTotalesTelaTergalForro();
                });
            }
        });

        const cortinaCheck = document.getElementById('cortinaCheck');
        const tergalCheck = document.getElementById('tergalCheck');
        const forroCheck = document.getElementById('forroCheck');
        const seccionCortina = document.getElementById('seccion-cortina');
        const seccionTergal = document.getElementById('seccion-tergal');
        const seccionForro = document.getElementById('seccion-forro');

        function mostrarOcultarSecciones() {
            if (seccionCortina) seccionCortina.style.display = cortinaCheck.checked ? '' : 'none';
            if (seccionTergal) seccionTergal.style.display = tergalCheck.checked ? '' : 'none';
            if (seccionForro) seccionForro.style.display = forroCheck.checked ? '' : 'none';
            
            calcularTotalesTelaTergalForro();
        }

        if (cortinaCheck) cortinaCheck.addEventListener('change', mostrarOcultarSecciones);
        if (tergalCheck) tergalCheck.addEventListener('change', mostrarOcultarSecciones);
        if (forroCheck) forroCheck.addEventListener('change', mostrarOcultarSecciones);

        setTimeout(function() {
            selectConfigs.forEach(config => {
                const selectElement = document.getElementById(config.selectId);
                if (selectElement && selectElement.value) {
                    actualizarPrecioIndividual(selectElement, config.precioInputId);
                }
            });

            mostrarOcultarSecciones();
            calcularTotalesTelaTergalForro();
        }, 100);
    });
    
    // Función para calcular mano de obra desde totales
    function calcularManoObraDesdeTotales() {
        try {
            const m2Cortina = parseFloat(document.getElementById('total_tela')?.value) || 0;
            const m2Tergal = parseFloat(document.getElementById('total_tergal')?.value) || 0;

            const precioMO1 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_1]"]')?.value) || 0;
            const precioMO2 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_2]"]')?.value) || 0;

            const totalMO1 = m2Cortina * precioMO1;
            const totalMO2 = m2Tergal * precioMO2;
            const totalMO = totalMO1 + totalMO2;

            const m2CortinaInput = document.querySelector('[name="detalle[m2_1]"]');
            const m2TergalInput = document.querySelector('[name="detalle[m2_2]"]');
            const totalMO1Input = document.querySelector('[name="detalle[total_mano_obra_1]"]');
            const totalMO2Input = document.querySelector('[name="detalle[total_mano_obra_2]"]');
            const totalMOInput = document.querySelector('[name="detalle[costo_total_mano_obra]"]');

            if (m2CortinaInput) m2CortinaInput.value = m2Cortina.toFixed(2);
            if (m2TergalInput) m2TergalInput.value = m2Tergal.toFixed(2);
            if (totalMO1Input) totalMO1Input.value = totalMO1.toFixed(2);
            if (totalMO2Input) totalMO2Input.value = totalMO2.toFixed(2);
            if (totalMOInput) totalMOInput.value = totalMO.toFixed(2);

            console.log('Mano de obra recalculada:', {
                m2Cortina: m2Cortina.toFixed(2),
                m2Tergal: m2Tergal.toFixed(2),
                totalMO1: totalMO1.toFixed(2),
                totalMO2: totalMO2.toFixed(2),
                totalMO: totalMO.toFixed(2)
            });
        } catch (error) {
            console.error('Error al calcular mano de obra:', error);
        }
    }

    // REEMPLAZA tu DOMContentLoaded existente con este código consolidado
    document.addEventListener('DOMContentLoaded', function() {
        // Calcular mano de obra inicial
        calcularManoObraDesdeTotales();

        // ===== LISTENERS PARA RECALCULAR MANO DE OBRA AUTOMÁTICAMENTE =====
        
        // 1. Cuando cambian los totales de tela/tergal (valores principales)
        ['total_tela', 'total_tergal'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    console.log(`Cambio en ${id}:`, this.value);
                    calcularManoObraDesdeTotales();
                });
                el.addEventListener('change', calcularManoObraDesdeTotales);
            }
        });

        // 2. Cuando cambian los precios de mano de obra
        ['detalle[costo_mano_obra_1]', 'detalle[costo_mano_obra_2]'].forEach(function(name) {
            const input = document.querySelector(`[name="${name}"]`);
            if (input) {
                input.addEventListener('input', function() {
                    console.log(`Cambio en precio MO:`, name, this.value);
                    calcularManoObraDesdeTotales();
                });
                input.addEventListener('change', calcularManoObraDesdeTotales);
            }
        });

        // 3. Cuando se modifican manualmente los m2 (para recalcular solo los totales de MO)
        ['detalle[m2_1]', 'detalle[m2_2]'].forEach(function(name) {
            const input = document.querySelector(`[name="${name}"]`);
            if (input) {
                input.addEventListener('input', function() {
                    console.log(`Cambio manual en m2:`, name, this.value);
                    
                    // Solo recalcular totales de MO, no sobreescribir los m2
                    const m2CortinaManual = parseFloat(document.querySelector('[name="detalle[m2_1]"]')?.value) || 0;
                    const m2TergalManual = parseFloat(document.querySelector('[name="detalle[m2_2]"]')?.value) || 0;
                    const precioMO1 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_1]"]')?.value) || 0;
                    const precioMO2 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_2]"]')?.value) || 0;

                    const totalMO1 = m2CortinaManual * precioMO1;
                    const totalMO2 = m2TergalManual * precioMO2;
                    const totalMO = totalMO1 + totalMO2;

                    const totalMO1Input = document.querySelector('[name="detalle[total_mano_obra_1]"]');
                    const totalMO2Input = document.querySelector('[name="detalle[total_mano_obra_2]"]');
                    const totalMOInput = document.querySelector('[name="detalle[costo_total_mano_obra]"]');

                    if (totalMO1Input) totalMO1Input.value = totalMO1.toFixed(2);
                    if (totalMO2Input) totalMO2Input.value = totalMO2.toFixed(2);
                    if (totalMOInput) totalMOInput.value = totalMO.toFixed(2);
                });
            }
        });

        // ===== INTEGRACIÓN CON LA FUNCIÓN calcularTotalesTelaTergalForro =====
        // Modificar la función existente para que también dispare el recálculo de MO
        const funcionOriginal = window.calcularTotalesTelaTergalForro;
        if (typeof funcionOriginal === 'function') {
            window.calcularTotalesTelaTergalForro = function() {
                // Ejecutar la función original
                funcionOriginal.apply(this, arguments);
                
                // Después ejecutar el recálculo de mano de obra
                setTimeout(calcularManoObraDesdeTotales, 10);
            };
        }

        console.log('Listeners para mano de obra configurados correctamente');
    });

    // ===== FUNCIÓN OPCIONAL PARA DEBUGGING =====
    function verificarCamposManoObra() {
        const campos = {
            'total_tela': document.getElementById('total_tela')?.value,
            'total_tergal': document.getElementById('total_tergal')?.value,
            'costo_mano_obra_1': document.querySelector('[name="detalle[costo_mano_obra_1]"]')?.value,
            'costo_mano_obra_2': document.querySelector('[name="detalle[costo_mano_obra_2]"]')?.value,
            'm2_1': document.querySelector('[name="detalle[m2_1]"]')?.value,
            'm2_2': document.querySelector('[name="detalle[m2_2]"]')?.value,
            'total_mano_obra_1': document.querySelector('[name="detalle[total_mano_obra_1]"]')?.value,
            'total_mano_obra_2': document.querySelector('[name="detalle[total_mano_obra_2]"]')?.value,
            'costo_total_mano_obra': document.querySelector('[name="detalle[costo_total_mano_obra]"]')?.value
        };
        
        console.table(campos);
        return campos;
    }
</script>

{{-- Script para insumos --}}
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

        // Insumos fijos
        const insumosFijos = [
            { cantidad: 'ojillos_cantidad', precio: '{{ $insumosFijos["Ojillos"]->precio_publico ?? 0 }}' },
            { cantidad: 'cortinero_cantidad', precio: document.getElementById('cortinero_precio') ? document.getElementById('cortinero_precio').value : 0 },
            { cantidad: 'puntas_cantidad', precio: '{{ $insumosFijos["Puntas"]->precio_publico ?? 0 }}' },
            { cantidad: 'mensulas_cantidad', precio: '{{ $insumosFijos["Mensulas"]->precio_publico ?? 0 }}' }
        ];

        insumosFijos.forEach(insumo => {
            const cantidadInput = document.querySelector(`[name="detalle[${insumo.cantidad}]"]`);
            const cantidad = parseFloat(cantidadInput?.value) || 0;
            const precio = parseFloat(insumo.precio) || 0;
            total += cantidad * precio;
        });

        // Insumos dinámicos (otros insumos)
        document.querySelectorAll('#materiales-tbody tr.otro-insumo-row').forEach(fila => {
            const cantidadInput = fila.querySelector('input[name*="_cantidad"]');
            const precioInput = fila.querySelector('input[name*="_precio"]');
            const cantidad = parseFloat(cantidadInput?.value) || 0;
            const precio = parseFloat(precioInput?.value) || 0;
            total += cantidad * precio;
        });

        document.getElementById('costo_total_materiales').value = total.toFixed(2);
    }

    // Cambios en insumos fijos y dinámicos
    document.addEventListener('input', function(e) {
        if (
            e.target.name === 'detalle[ojillos_cantidad]' ||
            e.target.name === 'detalle[cortinero_cantidad]' ||
            e.target.name === 'detalle[puntas_cantidad]' ||
            e.target.name === 'detalle[mensulas_cantidad]' ||
            e.target.name?.includes('_cantidad') ||
            e.target.name?.includes('_precio')
        ) {
            actualizarCostoTotalMateriales();
        }
    });

    // Cambio de cortinero para actualizar el precio y el total
    document.addEventListener('change', function(e) {
        if (e.target.id === 'cortinero_id') {
            const cortineroPrecio = document.getElementById('cortinero_precio');
            const selected = e.target.options[e.target.selectedIndex];
            cortineroPrecio.value = selected.dataset.precio || '';
            actualizarCostoTotalMateriales();
        }
    });

    // Actualizar al cargar
    document.addEventListener('DOMContentLoaded', actualizarCostoTotalMateriales);
</script>

{{-- Cálculos tabla totales --}}
<script>
    function calcularTotales() {
        // Totales de lienzos
        const totalLienzosCortina = parseFloat(document.getElementById('no_lienzos_redondeado')?.value) || 0;
        const totalLienzosTergal = parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value) || 0;
        const totalLienzosForro = parseFloat(document.getElementById('no_lienzos_redondeado_forro')?.value) || 0;
        const totalLienzos = totalLienzosCortina + totalLienzosTergal + totalLienzosForro;
        if (document.getElementById('total_lienzos')) {
            document.getElementById('total_lienzos').value = totalLienzos > 0 ? totalLienzos : '';
        }

        // Forro, Tela, Tergal
        const totalForro = parseFloat(document.getElementById('total_forro')?.value) || 0;
        if (document.getElementById('total_m2_forro')) {
            document.getElementById('total_m2_forro').value = totalForro > 0 ? totalForro.toFixed(2) : '';
        }
        const totalTela = parseFloat(document.getElementById('total_tela')?.value) || 0;
        if (document.getElementById('total_m2_tela')) {
            document.getElementById('total_m2_tela').value = totalTela > 0 ? totalTela.toFixed(2) : '';
        }
        const totalTergal = parseFloat(document.getElementById('total_tergal')?.value) || 0;
        if (document.getElementById('total_m2_tergal')) {
            document.getElementById('total_m2_tergal').value = totalTergal > 0 ? totalTergal.toFixed(2) : '';
        }

        // Cálculos monetarios
        const costoTelaTergal = parseFloat(document.getElementById('costo_total_tela_tergal_forro')?.value) || 0;
        const costoForro = parseFloat(document.querySelector('[name="detalle[total_final_forro]"]')?.value) || 0;
        const costoManoObra = parseFloat(document.querySelector('[name="detalle[costo_total_mano_obra]"]')?.value) || 0;
        const costoMateriales = parseFloat(document.getElementById('costo_total_materiales')?.value) || 0;
        const costoCortina = costoTelaTergal + costoForro + costoManoObra + costoMateriales;
        if (document.getElementById('costo_cortina')) {
            document.getElementById('costo_cortina').value = costoCortina > 0 ? costoCortina.toFixed(2) : '';
        }

        // Utilidad (puedes ajustar la fórmula si es diferente)
        const utilidad = costoCortina * 2;
        if (document.getElementById('utilidad')) {
            document.getElementById('utilidad').value = utilidad > 0 ? utilidad.toFixed(2) : '';
        }

        // Costo decorador
        const decoradorPorcentajeInput = document.getElementById('decorador_porcentaje');
        const decoradorPorcentaje = decoradorPorcentajeInput ? (parseFloat(decoradorPorcentajeInput.value) || 0) : 15;
        const costoDecorador = costoCortina + (costoCortina * (decoradorPorcentaje / 100));
        if (document.getElementById('costo_decorador')) {
            document.getElementById('costo_decorador').value = costoDecorador > 0 ? costoDecorador.toFixed(2) : '';
        }

        // Precio público (puedes ajustar la fórmula si es diferente)
        const precioPublico = costoCortina * 2;
        if (document.getElementById('precio_publico')) {
            document.getElementById('precio_publico').value = precioPublico > 0 ? precioPublico.toFixed(2) : '';
        }
    }

    // Actualizar al cargar
    document.addEventListener('DOMContentLoaded', calcularTotales);

    // Recalcula totales siempre que cambie cualquier input o select en el formulario
    document.addEventListener('input', function(e) {
        calcularTotales();
    });
    document.addEventListener('change', function(e) {
        calcularTotales();
    });
</script>
@endsection