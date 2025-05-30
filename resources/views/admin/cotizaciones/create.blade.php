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
                <div class="col-md-6 mb-3">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control" required autocomplete="off">
                        <option value="">Seleccione un cliente</option>
                        @foreach(\App\Models\Cliente::where('borrado', 0)->orderBy('nombre')->get() as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ date('Y-m-d') }}">
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

            <div class="card mt-4 d-none" id="tabla-totales-tela-tergal">
                <div class="card-header pb-1">
                    <h5 class="mb-1">Totales Tela, Tergal y Forro</h5>
                </div>
                <div class="card-body pt-2 p-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 180px;">Total Tela, Tergal y Forro</th>
                                            <th style="min-width: 150px;">Precio m²</th>
                                            <th style="min-width: 180px;">Descripción</th>
                                            <th style="min-width: 150px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Fila Cortina -->
                                        <tr>
                                            <td>
                                                <input type="number" name="detalle[total_tela]" id="total_tela" class="form-control" step="0.01">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[precio_m2_tela]" id="precio_m2_tela" class="form-control" step="0.01" value="100.00">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="detalle[descripcion_tela]" class="form-control" placeholder="Cortina">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[total_tela_final]" id="total_tela_final" class="form-control" step="0.01">
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Fila Tergal -->
                                        <tr>
                                            <td>
                                                <input type="number" name="detalle[total_tergal]" id="total_tergal" class="form-control" step="0.01">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[precio_m2_tergal]" id="precio_m2_tergal" class="form-control" step="0.01" value="70.00">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="detalle[descripcion_tergal]" class="form-control" placeholder="Tergal">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[total_tergal_final]" id="total_tergal_final" class="form-control" step="0.01">
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Fila Forro -->
                                        <tr>
                                            <td>
                                                <input type="number" id="total_forro" name="detalle[total_forro]" class="form-control" step="0.01">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[precio_m2_forro]" id="precio_m2_forro" class="form-control" step="0.01" value="35.00" onchange="actualizarCostoTotal()">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="detalle[descripcion_forro]" class="form-control" placeholder="Forro">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[total_final_forro]" id="total_final_forro" class="form-control" step="0.01">
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Total general -->
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Costo total tela, tergal y forro:</strong></td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="detalle[costo_total_tela_tergal_forro]" id="costo_total_tela_tergal_forro" class="form-control" step="0.01">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 d-none" id="tabla-mano-obra">
                <div class="card-header pb-1">
                    <h5 class="mb-1">Mano de Obra</h5>
                </div>
                <div class="card-body pt-2 p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 120px;">m²</th>
                                    <th style="min-width: 180px;">Costo Mano de Obra</th>
                                    <th style="min-width: 150px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <label for="m2_2" class="me-2 mb-0" style="margin-right: 0.6rem;">Cortina</label>
                                            <input type="number" name="detalle[m2_1]" class="form-control" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                name="detalle[costo_mano_obra_1]"
                                                class="form-control"
                                                step="0.01"
                                                value="{{ $manoObra['Mano de Obra Cortina']->precio_publico ?? '' }}"
                                                readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_mano_obra_1]" class="form-control" step="0.01">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <label for="m2_2" class="me-2 mb-0" style="margin-right: 1rem;">Tergal</label>
                                            <input type="number" name="detalle[m2_2]" id="m2_2" class="form-control" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                name="detalle[costo_mano_obra_2]"
                                                class="form-control"
                                                step="0.01"
                                                value="{{ $manoObra['Mano de Obra Tergal']->precio_publico ?? '' }}"
                                                readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[total_mano_obra_2]" class="form-control" step="0.01">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Costo Total Mano de Obra:</strong></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="detalle[costo_total_mano_obra]" class="form-control" step="0.01">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4 d-none" id="tabla-materiales-varios">
                <div class="card-header pb-1">
                    <h5 class="mb-1">Materiales Varios</h5>
                </div>
                <div class="card-body pt-2 p-0">
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
                                <!-- Insumos fijos -->
                                <tr>
                                    <td>
                                        Ojillos
                                        <input type="hidden" name="detalle[ojillos_id]" value="{{ $insumosFijos['Ojillos']->id ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[ojillos_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{ $insumosFijos['Ojillos']->precio_publico ?? '' }}" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        Cortinero
                                        <select name="detalle[cortinero_id]" id="cortinero_id" class="form-select">
                                            <option value="">Seleccione tipo de cortinero</option>
                                            @foreach($cortineros as $cortinero)
                                            <option value="{{ $cortinero->id }}" data-precio="{{ $cortinero->precio_publico }}">
                                                {{ $cortinero->nombre }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[cortinero_cantidad]" id="cortinero_cantidad" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" id="cortinero_precio" name="detalle[cortinero_precio]" class="form-control" step="0.01" readonly>
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const cortineroSelect = document.getElementById('cortinero_id');
                                                const cortineroPrecio = document.getElementById('cortinero_precio');
                                                if (cortineroSelect && cortineroPrecio) {
                                                    cortineroSelect.addEventListener('change', function() {
                                                        const selected = cortineroSelect.options[cortineroSelect.selectedIndex];
                                                        cortineroPrecio.value = selected.dataset.precio || '';
                                                        actualizarCostoTotal();
                                                        actualizarTablaTotales();
                                                    });
                                                    // Actualiza el precio al cargar si hay uno seleccionado
                                                    const selected = cortineroSelect.options[cortineroSelect.selectedIndex];
                                                    cortineroPrecio.value = selected && selected.dataset.precio ? selected.dataset.precio : '';
                                                }
                                            });
                                        </script>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        Puntas
                                        <input type="hidden" name="detalle[puntas_id]" value="{{ $insumosFijos['Puntas']->id ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[puntas_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{ $insumosFijos['Puntas']->precio_publico ?? '' }}" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        Mensulas
                                        <input type="hidden" name="detalle[mensulas_id]" value="{{ $insumosFijos['Mensulas']->id ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" name="detalle[mensulas_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" value="{{ $insumosFijos['Mensulas']->precio_publico ?? '' }}" step="0.01" readonly>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
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

            <div class="card mt-4 d-none" id="tabla-totales">
                <div class="card-header pb-1">
                    <h5 class="mb-1">Totales</h5>
                </div>
                <div class="card-body pt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered mb-0 align-middle">
                                <tbody>
                                    <tr>
                                        <td><strong>Total No. Lienzos</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_lienzos" name="totales[total_lienzos]" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Forro</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_forro" name="totales[total_m2_forro]" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Tela</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_tela" name="totales[total_m2_tela]" readonly>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total m² Tergal</strong></td>
                                        <td>
                                            <input type="number" class="form-control" id="total_m2_tergal" name="totales[total_m2_tergal]" readonly>
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
                                                <input type="number" class="form-control" id="costo_cortina" name="totales[costo_cortina]" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Utilidad</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="utilidad" name="totales[utilidad]" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Costo Decorador</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" id="decorador_porcentaje" class="form-control text-end" value="15" min="0" max="100" step="0.01" style="max-width: 100px;">
                                                <span class="input-group-text">%</span>
                                                <span class="input-group-text" style="margin-left: 0.5rem;">$</span>
                                                <input type="number" class="form-control" id="costo_decorador" name="totales[costo_decorador]" readonly>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Precio Público</strong></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" id="precio_publico" name="totales[precio_publico]" readonly>
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
    <option value="{{ $tela->id }}" data-precio="{{ $precio }}">
        {{ $tela->nombre }} - {{ $tela->campo1 }} - {{ $tela->campo2 }}
    </option>
    @endforeach
</select>

<select id="plantilla_tergal" class="d-none">
    <option value="">Seleccione un tergal</option>
    @foreach($tergales as $tergal)
    <option value="{{ $tergal->id }}" data-precio="{{ is_numeric($tergal->precio_publico) ? $tergal->precio_publico : 0 }}">
        {{ $tergal->nombre }} - {{ $tergal->campo1 }} - {{ $tergal->campo2 }}
    </option>
    @endforeach
</select>

<select id="plantilla_forro" class="d-none">
    <option value="">Seleccione un forro</option>
    @foreach($forros as $forro)
    <option value="{{ $forro->id }}" data-precio="{{ is_numeric($forro->precio_publico) ? $forro->precio_publico : 0 }}">
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
                                <div class="card mb-4">
                                    <div class="card-header pb-1">
                                        <h5 class="mb-1">Datos de la Cortina</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div class="mb-3">
                                            <label for="tela_id" class="form-label">Tela</label>
                                            <select id="tela_id" name="detalle[tela_id]" class="form-control select2" required
                                                oninvalid="this.setCustomValidity('Por favor selecciona una tela')"
                                                oninput="this.setCustomValidity('')">
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mt-2 align-middle">
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
                                                            <input type="text" name="detalle[ancho_tela]" id="ancho_tela" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detalle[ancho]" id="ancho" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detalle[largo]" id="largo" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="double" name="detalle[no_lienzos]" id="no_lienzos" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_redondeado]" id="no_lienzos_redondeado" class="form-control" onchange="actualizarTablaTotales()">
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <input type="number" name="detalle[valor_bastilla]" id="valor_bastilla" class="form-control" placeholder="Ej. 1.10m" step="0.01" min="0">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                    const precio = $(this).find('option:selected').data('precio');
                                    $('#precio_m2_tela').val(Number(precio).toFixed(2)).trigger('input');

                                    const metros = parseFloat($('#total_tela').val()) || 0;
                                    const total = metros * Number(precio);
                                    $('#total_tela_final').val(total.toFixed(2));

                                    const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
                                    const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((total + totalTergalFinal + totalForroFinal).toFixed(2));

                                    actualizarTablaTotales();
                                });

                                $(telaSelect).trigger('change');
                            }, 0);
                        }

                        if (tergal.checked) {
                            formDinamico.innerHTML += `
                                <div class="card mb-4">
                                    <div class="card-header pb-1">
                                        <h5 class="mb-1">Datos del Tergal</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div class="mb-3">
                                            <label for="tergal_id" class="form-label">Tergal</label>
                                            <select id="tergal_id" name="detalle[tergal_id]" class="form-control select2" required
                                                oninvalid="this.setCustomValidity('Por favor selecciona un tergal')"
                                                oninput="this.setCustomValidity('')">
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mt-2 align-middle">
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
                                                            <input type="text" name="detalle[ancho_tergal]" id="ancho_tergal" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detalle[ancho_tergal_real]" id="ancho_tergal_real" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detalle[largo_tergal]" id="largo_tergal" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="double" name="detalle[no_lienzos_tergal]" id="no_lienzos_tergal" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_redondeado_tergal]" id="no_lienzos_redondeado_tergal" class="form-control" onchange="actualizarTablaTotales()">
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <input type="number" name="detalle[valor_bastilla_tergal]" id="valor_bastilla_tergal" class="form-control" placeholder="Ej. 0.65m" step="0.01" min="0">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            setTimeout(() => {
                                const anchoCortina = document.getElementById('ancho');
                                const largoCortina = document.getElementById('largo');
                                const anchoTelaCortina = document.getElementById('ancho_tela');

                                const anchoTergal = document.getElementById('ancho_tergal_real');
                                const largoTergal = document.getElementById('largo_tergal');
                                const anchoTelaTergal = document.getElementById('ancho_tergal');
                                const noLienzosTergal = document.getElementById('no_lienzos_tergal');
                                const noLienzosRedondeadoTergal = document.getElementById('no_lienzos_redondeado_tergal');

                                largoTergal.addEventListener('blur', () => {
                                    let val = parseFloat(largoTergal.value);
                                    if (!isNaN(val)) {
                                        largoTergal.value = val.toFixed(2);
                                    }
                                });

                                function calcularTergal() {
                                    // Usa los campos de tergal, no los de cortina
                                    let ancho = parseFloat(document.getElementById('ancho_tergal_real')?.value);
                                    let anchoTela = parseFloat(document.getElementById('ancho_tergal')?.value);

                                    if (!isNaN(ancho) && !isNaN(anchoTela) && anchoTela > 0) {
                                        let lienzos = (ancho * 2.5) / anchoTela;
                                        document.getElementById('no_lienzos_tergal').value = lienzos.toFixed(2);
                                        document.getElementById('no_lienzos_redondeado_tergal').value = Math.ceil(lienzos);
                                    } else {
                                        document.getElementById('no_lienzos_tergal').value = '';
                                        document.getElementById('no_lienzos_redondeado_tergal').value = '';
                                    }
                                }
                                const plantillaTergal = document.getElementById('plantilla_tergal');
                                const tergalSelect = document.getElementById('tergal_id');
                                tergalSelect.innerHTML = plantillaTergal.innerHTML;

                                if (tergalSeleccionado) {
                                    $(tergalSelect).val(tergalSeleccionado);
                                }


                                $(tergalSelect).select2();

                                $(tergalSelect).on('change', function() {
                                    const precio = $(this).find('option:selected').data('precio');
                                    $('#precio_m2_tergal').val(Number(precio).toFixed(2)).trigger('input');

                                    const metros = parseFloat($('#total_tergal').val()) || 0;
                                    const total = metros * Number(precio);
                                    $('#total_tergal_final').val(total.toFixed(2));

                                    const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
                                    const totalForroFinal = parseFloat($('#total_final_forro').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((totalTelaFinal + total + totalForroFinal).toFixed(2));

                                    actualizarTablaTotales();
                                });

                                function sincronizarTergalConCortina() {
                                    // Si hay datos en los campos de cortina, se heredan
                                    if (anchoCortina?.value && anchoTelaCortina?.value) {
                                        let largoOriginal = largoCortina?.dataset?.original ?
                                            parseFloat(largoCortina.dataset.original) :
                                            parseFloat(largoCortina?.value);

                                        anchoTergal.value = anchoCortina.value;

                                        const bastillaTergalInput = document.getElementById('valor_bastilla_tergal');

                                        if (bastillaTergalInput && (bastillaTergalInput.value === '' || parseFloat(bastillaTergalInput.value) === 0)) {
                                            if (!isNaN(largoOriginal)) {
                                                largoTergal.value = largoOriginal.toFixed(2);
                                                largoTergal.dataset.original = largoOriginal;
                                            } else {
                                                largoTergal.value = '';
                                                largoTergal.dataset.original = '';
                                            }
                                        }

                                        anchoTelaTergal.value = anchoTelaCortina.value;


                                        calcularTergal();
                                    } else {
                                        anchoTergal.readOnly = false;
                                        largoTergal.readOnly = false;
                                        anchoTelaTergal.readOnly = false;
                                    }

                                    $(tergalSelect).trigger('change');
                                }

                                // Escuchar cambios en inputs para actualizar tergal si los datos de cortina cambian
                                ['ancho', 'largo', 'ancho_tela'].forEach(id => {
                                    const input = document.getElementById(id);
                                    if (input) {
                                        input.addEventListener('input', sincronizarTergalConCortina);
                                    }
                                });

                                // Escuchar cambios manuales para tergal si se escriben directamente
                                [anchoTergal, anchoTelaTergal].forEach(input => {
                                    input.addEventListener('input', calcularTergal);
                                });

                                sincronizarTergalConCortina();
                            }, 200);
                        }

                        if (forro.checked) {
                            formDinamico.innerHTML += `
                                <div class="card mb-4">
                                    <div class="card-header pb-1">
                                        <h5 class="mb-1">Datos del Forro</h5>
                                    </div>
                                    <div class="card-body pt-2">
                                        <div class="mb-3">
                                            <label for="forro_id" class="form-label">Forro</label>
                                            <select id="forro_id" name="detalle[forro_id]" class="form-control select2" required
                                                oninvalid="this.setCustomValidity('Por favor selecciona un forro')"
                                                oninput="this.setCustomValidity('')">
                                            </select>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mt-2 align-middle">
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
                                                            <input type="text" name="detalle[ancho_forro]" id="ancho_forro" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detalle[ancho_forro_real]" id="ancho_forro_real" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="detalle[largo_forro]" id="largo_forro" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="double" name="detalle[no_lienzos_forro]" id="no_lienzos_forro" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="detalle[no_lienzos_redondeado_forro]" id="no_lienzos_redondeado_forro" class="form-control" onchange="actualizarTablaTotales()">
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <input type="number" name="detalle[valor_bastilla_forro]" id="valor_bastilla_forro" class="form-control" placeholder="Ej. 0.40m" step="0.01" min="0">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                            setTimeout(() => {
                                const anchoCortina = document.getElementById('ancho');
                                const largoCortina = document.getElementById('largo');
                                const anchoTelaCortina = document.getElementById('ancho_tela');

                                const anchoForro = document.getElementById('ancho_forro_real');
                                const largoForro = document.getElementById('largo_forro');
                                const anchoTelaForro = document.getElementById('ancho_forro');
                                const noLienzosForro = document.getElementById('no_lienzos_forro');
                                const noLienzosRedondeadoForro = document.getElementById('no_lienzos_redondeado_forro');
                                const totalForro = document.getElementById('total_forro');
                                const precioM2 = document.querySelector('[name="detalle[precio_m2_forro]"]');
                                const totalFinal = document.querySelector('[name="detalle[total_final_forro]"]');
                                const costoTotal = document.querySelector('[name="detalle[costo_total_forro]"]');
                                const plantillaForro = document.getElementById('plantilla_forro');
                                const forroSelect = document.getElementById('forro_id');

                                largoForro.addEventListener('blur', () => {
                                    let val = parseFloat(largoForro.value);
                                    if (!isNaN(val)) {
                                        largoForro.value = val.toFixed(2);
                                    }
                                });

                                function calcularForro() {
                                    let ancho = parseFloat(anchoForro.value);
                                    let anchoTela = parseFloat(anchoTelaForro.value);

                                    if (!isNaN(ancho) && !isNaN(anchoTela) && anchoTela > 0) {
                                        let lienzos = (ancho * 2.5) / anchoTela;
                                        noLienzosForro.value = lienzos.toFixed(2);
                                        noLienzosRedondeadoForro.value = Math.ceil(lienzos);
                                    }
                                }

                                forroSelect.innerHTML = plantillaForro.innerHTML;

                                if (forroSeleccionado) {
                                    $(forroSelect).val(forroSeleccionado);
                                }

                                $(forroSelect).select2();

                                $(forroSelect).on('change', function() {
                                    const precio = $(this).find('option:selected').data('precio');
                                    $('#precio_m2_forro').val(Number(precio).toFixed(2)).trigger('input');

                                    const metros = parseFloat($('#total_forro').val()) || 0;
                                    const total = metros * Number(precio);
                                    $('#total_final_forro').val(total.toFixed(2));

                                    const totalTelaFinal = parseFloat($('#total_tela_final').val()) || 0;
                                    const totalTergalFinal = parseFloat($('#total_tergal_final').val()) || 0;
                                    $('#costo_total_tela_tergal_forro').val((totalTelaFinal + totalTergalFinal + total).toFixed(2));

                                    actualizarTablaTotales();
                                });

                                function sincronizarForroConCortina() {
                                    // Campos de cortina
                                    const anchoCortina = document.getElementById('ancho');
                                    const largoCortina = document.getElementById('largo');
                                    const anchoTelaCortina = document.getElementById('ancho_tela');

                                    // Actualización para que también funcione con el tergal

                                    // Campos de tergal
                                    const anchoTergal = document.getElementById('ancho_tergal_real');
                                    const largoTergal = document.getElementById('largo_tergal');
                                    const anchoTelaTergal = document.getElementById('ancho_tergal');
                                    // Campos de forro
                                    const anchoForro = document.getElementById('ancho_forro_real');
                                    const largoForro = document.getElementById('largo_forro');
                                    const anchoTelaForro = document.getElementById('ancho_forro');

                                    // Intenta primero con cortina, si no, con tergal
                                    let anchoBase = anchoCortina?.value || anchoTergal?.value || '';
                                    let largoBase = largoCortina?.value || largoTergal?.value || '';
                                    let anchoTelaBase = anchoTelaCortina?.value || anchoTelaTergal?.value || '';

                                    if (anchoBase && anchoTelaBase) {
                                        anchoForro.value = anchoBase;
                                        anchoTelaForro.value = anchoTelaBase;

                                        // Bastilla forro
                                        const bastillaForroInput = document.getElementById('valor_bastilla_forro');
                                        let largoOriginal = largoBase;
                                        if (bastillaForroInput && (bastillaForroInput.value === '' || parseFloat(bastillaForroInput.value) === 0)) {
                                            if (!isNaN(parseFloat(largoOriginal))) {
                                                largoForro.value = parseFloat(largoOriginal).toFixed(2);
                                                largoForro.dataset.original = parseFloat(largoOriginal);
                                            } else {
                                                largoForro.value = '';
                                                largoForro.dataset.original = '';
                                            }
                                        }
                                        // Si hay bastilla, el script de bastilla ya la suma
                                        calcularForro();
                                    } else {
                                        anchoForro.readOnly = false;
                                        largoForro.readOnly = false;
                                        anchoTelaForro.readOnly = false;
                                    }

                                    // Actualiza select2 y totales
                                    $(document.getElementById('forro_id')).trigger('change');
                                }

                                // Escuchar cambios en inputs para actualizar forro si los datos de cortina cambian
                                ['ancho', 'largo', 'ancho_tela', 'ancho_tergal_real', 'largo_tergal', 'ancho_tergal'].forEach(id => {
                                    const input = document.getElementById(id);
                                    if (input) {
                                        input.addEventListener('input', sincronizarForroConCortina);
                                    }
                                });

                                // Escuchar cambios manuales para forro si se escriben directamente
                                [anchoForro, anchoTelaForro].forEach(input => {
                                    input.addEventListener('input', calcularForro);
                                });

                                sincronizarForroConCortina();
                            }, 500);

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

    // Script para agregar bastilla a la cortina
    document.addEventListener('input', function() {
        const largoInput = document.getElementById('largo');
        const bastillaInput = document.getElementById('valor_bastilla');

        if (!largoInput || !bastillaInput) return;

        // Guardar el valor original solo si no existe
        if (!largoInput.dataset.original) {
            const original = parseFloat(largoInput.value);
            if (!isNaN(original)) {
                largoInput.dataset.original = original;
            }
        }

        const largoOriginal = parseFloat(largoInput.dataset.original);
        const bastilla = parseFloat(bastillaInput.value);

        if (isNaN(largoOriginal)) return;

        if (!isNaN(bastilla)) {
            largoInput.value = (largoOriginal + bastilla).toFixed(2);
        } else {
            largoInput.value = largoOriginal.toFixed(2);
        }

        // Disparar el evento de cambio para otros cálculos dependientes
        const event = new Event('input', {
            bubbles: true
        });
        largoInput.dispatchEvent(event);
    });


    // Script para agregar bastilla al tergal
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'valor_bastilla_tergal') {
            const largoTergalInput = document.getElementById('largo_tergal');
            const bastillaTergalInput = e.target;

            if (!largoTergalInput || !bastillaTergalInput) return;

            // Guardar el valor original si no existe aún
            if (!largoTergalInput.dataset.original || largoTergalInput.value === "") {
                const original = parseFloat(largoTergalInput.value);
                if (!isNaN(original) && original > 0) {
                    largoTergalInput.dataset.original = original;
                }
            }

            const largoOriginal = parseFloat(largoTergalInput.dataset.original);
            const bastilla = parseFloat(bastillaTergalInput.value);

            if (isNaN(largoOriginal)) return;

            if (!isNaN(bastilla)) {
                largoTergalInput.value = (largoOriginal + bastilla).toFixed(2);
            } else {
                largoTergalInput.value = largoOriginal.toFixed(2);
            }

            const event = new Event('input', {
                bubbles: true
            });
            largoTergalInput.dispatchEvent(event);
        }
    });

    // Script para agregar bastilla al forro
    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'valor_bastilla_forro') {
            const largoForroInput = document.getElementById('largo_forro');
            const bastillaForroInput = e.target;

            if (!largoForroInput || !bastillaForroInput) return;

            // Guardar el valor original si no existe aún
            if (!largoForroInput.dataset.original || largoForroInput.value === "") {
                const original = parseFloat(largoForroInput.value);
                if (!isNaN(original) && original > 0) {
                    largoForroInput.dataset.original = original;
                }
            }

            const largoOriginal = parseFloat(largoForroInput.dataset.original);
            const bastilla = parseFloat(bastillaForroInput.value);

            if (isNaN(largoOriginal)) return;

            if (!isNaN(bastilla)) {
                largoForroInput.value = (largoOriginal + bastilla).toFixed(2);
            } else {
                largoForroInput.value = largoOriginal.toFixed(2);
            }

            const event = new Event('input', {
                bubbles: true
            });
            largoForroInput.dispatchEvent(event);
        }
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
    }

    // Script para calcular el total de tela, tergal y forro para la tabla de totales y el costo de mano de obra
    document.addEventListener('change', function() {
        // Valores para Cortina
        const noLienzosCortina = parseFloat(document.getElementById('no_lienzos_redondeado')?.value);
        const largoCortina = parseFloat(document.getElementById('largo')?.value);
        const precioTela = parseFloat(document.getElementById('precio_m2_tela')?.value);

        // Valores para Tergal
        const noLienzosTergal = parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value);
        const largoTergal = parseFloat(document.getElementById('largo_tergal')?.value);
        const precioTergal = parseFloat(document.getElementById('precio_m2_tergal')?.value);

        // Valores para Forro
        const noLienzosForro = parseFloat(document.getElementById('no_lienzos_redondeado_forro')?.value);
        const largoForro = parseFloat(document.getElementById('largo_forro')?.value);
        const precioForro = parseFloat(document.getElementById('precio_m2_forro')?.value);

        // Cálculos de totales
        const totalTela = (!isNaN(noLienzosCortina) && !isNaN(largoCortina)) ? (noLienzosCortina * largoCortina) : 0;
        const totalTergal = (!isNaN(noLienzosTergal) && !isNaN(largoTergal)) ? (noLienzosTergal * largoTergal) : 0;
        const totalForro = (!isNaN(noLienzosForro) && !isNaN(largoForro)) ? (noLienzosForro * largoForro) : 0;

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

    let contadorOtros = 1;

    // Insumos precargados desde el backend
    const insumosDisponibles = @json($insumos); // Marca error pero funciona igual

    // Scripts para calcular el costo total de materiales
    function crearSelectInsumos(nombreInput) {
        const select = document.createElement('select');
        select.classList.add('form-select');
        select.name = nombreInput;

        const defaultOption = document.createElement('option');
        defaultOption.value = "";
        defaultOption.textContent = "Seleccione un insumo";
        select.appendChild(defaultOption);

        insumosDisponibles.forEach(insumo => {
            const option = document.createElement('option');
            option.value = insumo.id;
            option.textContent = insumo.nombre;
            select.appendChild(option);
        });

        return select;
    }

    function añadirOtroInsumo() {
        const tbody = document.getElementById('materiales-tbody');

        const fila = document.createElement('tr');

        // Celda de selección de insumo
        const tdNombre = document.createElement('td');
        tdNombre.appendChild(crearSelectInsumos(`detalle[otros${contadorOtros}_nombre]`));

        // Celda cantidad
        const tdCantidad = document.createElement('td');
        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.name = `detalle[otros${contadorOtros}_cantidad]`;
        inputCantidad.classList.add('form-control');
        inputCantidad.step = 1;
        inputCantidad.min = 0;
        inputCantidad.addEventListener('input', actualizarCostoTotal);
        tdCantidad.appendChild(inputCantidad);

        // Celda precio
        const tdPrecio = document.createElement('td');
        const inputGroup = document.createElement('div');
        inputGroup.classList.add('input-group');

        const span = document.createElement('span');
        span.classList.add('input-group-text');
        span.textContent = '$';

        const inputPrecio = document.createElement('input');
        inputPrecio.type = 'number';
        inputPrecio.name = `detalle[otros${contadorOtros}_precio]`;
        inputPrecio.classList.add('form-control');
        inputPrecio.step = 0.01;
        inputPrecio.min = 0;
        inputPrecio.addEventListener('input', actualizarCostoTotal);

        inputGroup.appendChild(span);
        inputGroup.appendChild(inputPrecio);
        tdPrecio.appendChild(inputGroup);

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
        fila.appendChild(tdEliminar);

        tbody.appendChild(fila);
        contadorOtros++;
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
    }

    function actualizarTablaTotales() {

        // Cálculo de telas de la tabla totales

        const totalLienzosCortina = parseFloat(document.getElementById('no_lienzos_redondeado')?.value) || 0;
        const totalLienzosTergal = parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value) || 0;
        const totalLienzosForro = parseFloat(document.getElementById('no_lienzos_redondeado_forro')?.value) || 0;
        const totalLienzos = totalLienzosCortina + totalLienzosTergal + totalLienzosForro;
        document.getElementById('total_lienzos').value = totalLienzos > 0 ? totalLienzos : '';

        const totalForro = parseFloat(document.getElementById('total_forro')?.value) || 0;
        document.getElementById('total_m2_forro').value = totalForro > 0 ? totalForro.toFixed(2) : '';

        const totalTela = parseFloat(document.getElementById('total_tela')?.value) || 0;
        document.getElementById('total_m2_tela').value = totalTela > 0 ? totalTela.toFixed(2) : '';

        const totalTergal = parseFloat(document.getElementById('total_tergal')?.value) || 0;
        document.getElementById('total_m2_tergal').value = totalTergal > 0 ? totalTergal.toFixed(2) : '';

        // Cálculos monetarios de la tabla totales

        const costoTelaTergal = parseFloat(document.getElementById('costo_total_tela_tergal_forro')?.value) || 0;
        const costoForro = parseFloat(document.querySelector('[name="detalle[total_final_forro]"]')?.value) || 0;
        const costoManoObra = parseFloat(document.querySelector('[name="detalle[costo_total_mano_obra]"]')?.value) || 0;
        const costoMateriales = parseFloat(document.getElementById('costo_total_materiales')?.value) || 0;

        const costoCortina = costoTelaTergal + costoForro + costoManoObra + costoMateriales;
        document.getElementById('costo_cortina').value = costoCortina > 0 ? costoCortina.toFixed(2) : '';

        const utilidad = costoCortina * 2;
        document.getElementById('utilidad').value = utilidad > 0 ? utilidad.toFixed(2) : '';

        const decoradorPorcentajeInput = document.getElementById('decorador_porcentaje');
        const decoradorPorcentaje = decoradorPorcentajeInput ? (parseFloat(decoradorPorcentajeInput.value) || 0) : 15;
        const costoDecorador = costoCortina + (costoCortina * (decoradorPorcentaje / 100));
        document.getElementById('costo_decorador').value = costoDecorador > 0 ? costoDecorador.toFixed(2) : '';

        const precioPublico = costoCortina * 2;
        document.getElementById('precio_publico').value = precioPublico > 0 ? precioPublico.toFixed(2) : '';
    }

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

    document.addEventListener('input', function(e) {
        if (
            e.target.id === 'no_lienzos_redondeado' ||
            e.target.id === 'largo' ||
            e.target.id === 'precio_m2_tela'
        ) {
            actualizarTablaTotales();
        }
    });

    $(document).on('change', '#tela_id', function() {
        const precio = $(this).find('option:selected').data('precio');
        $('#precio_m2_tela').val(Number(precio).toFixed(2));

        const metros = parseFloat($('#total_tela').val()) || 0;
        const total = metros * Number(precio);
        $('#total_tela_final').val(total.toFixed(2));

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

    document.getElementById('decorador_porcentaje').addEventListener('input', actualizarTablaTotales);
</script>
@endsection