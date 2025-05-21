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
                        <input type="checkbox" id="cotinaCheck" name="tipo[]" value="cortina" class="form-check-input" autocomplete="off">
                        <label class="form-check-label" for="cotinaCheck">Cortina</label>
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

            <div id="form-dinamico">
                <!-- Formularios dinámicos -->
            </div>

            <div id="tabla-totales-tela-tergal" class="mt-4 d-none">
                <table class="table table-bordered mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>Total Tela y Tergal</th>
                            <th>Precio m²</th>
                            <th>Descripción</th>
                            <th>Total</th>
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

                        <!-- Total general -->
                        <tr>
                            <td colspan="3" class="text-end"><strong>Costo total tela y tergal:</strong></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[costo_total_tela_tergal]" id="costo_total_tela_tergal" class="form-control" step="0.01">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="tabla-mano-obra" class="mt-4 d-none">
                <table class="table table-bordered mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>m²</th>
                            <th>Costo Mano de Obra</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="number" name="detalle[m2_1]" class="form-control" step="0.01"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[costo_mano_obra_1]" class="form-control" step="0.01" value="120.00">
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
                            <td><input type="number" name="detalle[m2_2]" class="form-control" step="0.01"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[costo_mano_obra_2]" class="form-control" step="0.01" value="100.00">
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
                            <td colspan="2" class="text-end"><strong>Costo Mano de Obra:</strong></td>
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

            <div id="tabla-materiales-varios" class="mt-4 d-none">
                <table class="table table-bordered mt-4">
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
                        <tr>
                            <td>
                                Ojillos
                                <input type="hidden" name="detalle[ojillos_id]" value="{{ $insumosFijos['Ojillos']->id ?? '' }}">
                            </td>
                            <td><input type="number" name="detalle[ojillos_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[ojillos_precio]" class="form-control" value="15.00" step="0.01" oninput="actualizarCostoTotal()">
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                Cortinero
                                <input type="hidden" name="detalle[cortinero_id]" value="{{ $insumosFijos['Cortinero']->id ?? '' }}">
                            </td>
                            <td><input type="number" name="detalle[cortinero_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[cortinero_precio]" class="form-control" value="200" step="0.01" oninput="actualizarCostoTotal()">
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                Puntas
                                <input type="hidden" name="detalle[puntas_id]" value="{{ $insumosFijos['Puntas']->id ?? '' }}">
                            </td>
                            <td><input type="number" name="detalle[puntas_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[puntas_precio]" class="form-control" value="250.00" step="0.01" oninput="actualizarCostoTotal()">
                                </div>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                Mensulas
                                <input type="hidden" name="detalle[mensulas_id]" value="{{ $insumosFijos['Mensulas']->id ?? '' }}">
                            </td>
                            <td><input type="number" name="detalle[mensulas_cantidad]" class="form-control" oninput="actualizarCostoTotal()" autocomplete="off"></td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="detalle[mensulas_precio]" class="form-control" value="120.00" step="0.01" oninput="actualizarCostoTotal()" autocomplete="off">
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

            <div id="tabla-totales" class="mt-4 d-none">
                <h5><strong>Totales</strong></h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered mb-0">
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
                        <table class="table table-bordered mb-0">
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
                                            <span class="input-group-text">$</span>
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
            <button type="submit" class="btn btn-primary">Guardar Cotización</button>
            <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<script>
    //Script para mostrar y ocultar formularios dinámicos
    document.addEventListener('DOMContentLoaded', function() {
        const cortina = document.getElementById('cotinaCheck');
        const tergal = document.getElementById('tergalCheck');
        const forro = document.getElementById('forroCheck');
        const formDinamico = document.getElementById('form-dinamico');

        function actualizarFormulario() {
            const valoresPrevios = {};
            const atributosOriginales = {};
            const estadosCheckbox = {};

            formDinamico.querySelectorAll('input').forEach(input => {
                if (input.name) valoresPrevios[input.name] = input.value;
                if (input.type === 'checkbox' && input.id) estadosCheckbox[input.id] = input.checked;
                if (input.dataset && input.dataset.original !== undefined && input.id) atributosOriginales[input.id] = input.dataset.original;
            });

            formDinamico.innerHTML = '';

            if (cortina.checked) {
                formDinamico.innerHTML += `
                    <table class="table table-bordered mt-4">
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
                                        <div class="form-check form-check-inline mb-0">
                                            <input type="checkbox" id="agregar_bastilla" class="form-check-input me-2">
                                            <label class="form-check-label mb-0" for="agregar_bastilla">+40 cm</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `;
            }

            if (tergal.checked) {
                formDinamico.innerHTML += `
        <table class="table table-bordered mt-4">
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
                            <div class="form-check form-check-inline mb-0">
                                <input type="checkbox" id="agregar_bastilla_tergal" class="form-check-input me-2">
                                <label class="form-check-label mb-0" for="agregar_bastilla_tergal">+65 cm</label>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
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

                    function calcularTergal() {
                        let ancho = parseFloat(anchoTergal.value);
                        let anchoTela = parseFloat(anchoTelaTergal.value);

                        if (!isNaN(ancho) && !isNaN(anchoTela) && anchoTela > 0) {
                            let lienzos = (ancho * 2.5) / anchoTela;
                            noLienzosTergal.value = lienzos.toFixed(2);
                            noLienzosRedondeadoTergal.value = Math.ceil(lienzos);
                        }
                    }

                    function sincronizarTergalConCortina() {
                        // Si hay datos en los campos de cortina, se heredan
                        if (anchoCortina?.value && anchoTelaCortina?.value) {
                            let largoOriginal = largoCortina?.dataset?.original ?
                                parseFloat(largoCortina.dataset.original) :
                                parseFloat(largoCortina?.value);

                            anchoTergal.value = anchoCortina.value;

                            // Solo actualiza el original si la bastilla de tergal NO está marcada
                            const bastillaTergalCheckbox = document.getElementById('agregar_bastilla_tergal');
                            if (bastillaTergalCheckbox && !bastillaTergalCheckbox.checked) {
                                if (!isNaN(largoOriginal)) {
                                    largoTergal.value = (largoOriginal).toFixed(2);
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
                    <table class="table table-bordered mt-4">
                        <thead class="table-light">
                            <tr>
                                <th>Total Forro</th>
                                <th>Precio m²</th>
                                <th>Descripción</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" id="total_forro" name="detalle[total_forro]" class="form-control" step="0.01"/></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="detalle[precio_m2_forro]" id="precio_m2_forro" class="form-control" step="0.01" value="35.00">
                                    </div>
                                </td>
                                <td><input type="text" name="detalle[descripcion_forro]" class="form-control" placeholder="Forro"/></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="detalle[total_final_forro]" class="form-control" step="0.01">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `;

                setTimeout(() => {
                    const anchoTela = document.getElementById('ancho_tela');
                    const anchoTergal = document.getElementById('ancho_tergal');
                    const totalForro = document.getElementById('total_forro');
                    const precioM2 = document.querySelector('[name="detalle[precio_m2_forro]"]');
                    const totalFinal = document.querySelector('[name="detalle[total_final_forro]"]');
                    const costoTotal = document.querySelector('[name="detalle[costo_total_forro]"]');

                    function actualizarTotalForro() {
                        let valor = null;
                        if (anchoTela && anchoTela.value) {
                            valor = parseFloat(anchoTela.value);
                        } else if (anchoTergal && anchoTergal.value) {
                            valor = parseFloat(anchoTergal.value);
                        }
                        if (!isNaN(valor)) {
                            totalForro.value = valor.toFixed(2);
                            totalForro.dataset.original = valor;
                        } else {
                            totalForro.value = "";
                            totalForro.dataset.original = "";
                        }
                        recalcular();
                        actualizarTablaTotales();
                    }

                    // Función para recalcular total y costo total
                    const recalcular = () => {
                        const forro = parseFloat(totalForro.value);
                        const precio = parseFloat(precioM2.value);

                        if (!isNaN(forro) && !isNaN(precio)) {
                            const total = forro * precio;
                            totalFinal.value = total.toFixed(2);
                            costoTotal.value = total.toFixed(2);
                        } else {
                            totalFinal.value = "";
                            costoTotal.value = "";
                        }
                    };

                    // Agregar eventos para recalcular cuando se cambien los valores
                    totalForro.addEventListener('input', recalcular);
                    precioM2.addEventListener('input', recalcular);

                    // Escuchar cambios en ancho_tela y ancho_tergal
                    if (anchoTela) {
                        anchoTela.addEventListener('input', actualizarTotalForro);
                    }
                    if (anchoTergal) {
                        anchoTergal.addEventListener('input', actualizarTotalForro);
                    }

                    actualizarTotalForro();
                }, 0);

            }

            // Restaura valores guardados
            const nuevosInputs = formDinamico.querySelectorAll('input');
            nuevosInputs.forEach(input => {
                if (input.name && valoresPrevios.hasOwnProperty(input.name)) {
                    input.value = valoresPrevios[input.name];
                }
                if (input.type === 'checkbox' && estadosCheckbox.hasOwnProperty(input.id)) {
                    input.checked = estadosCheckbox[input.id];
                }
                if (input.dataset && atributosOriginales.hasOwnProperty(input.id)) {
                    input.dataset.original = atributosOriginales[input.id];
                }
            });
            const bastillaTergalCheckbox = document.getElementById('agregar_bastilla_tergal');
            if (bastillaTergalCheckbox && bastillaTergalCheckbox.checked) {
                bastillaTergalCheckbox.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }


        cortina.addEventListener('change', actualizarFormulario);
        tergal.addEventListener('change', actualizarFormulario);
        forro.addEventListener('change', actualizarFormulario);
    });

    // Script para agregar 40 cm de bastilla al largo
    document.addEventListener('change', function() {
        const largoInput = document.getElementById('largo');
        const bastillaCheckbox = document.getElementById('agregar_bastilla');

        if (!largoInput || !bastillaCheckbox) return;

        // Solo guarda el valor original si no existe
        if (!largoInput.dataset.original) {
            const original = parseFloat(largoInput.value);
            if (!isNaN(original)) {
                largoInput.dataset.original = original;
            }
        }

        const largoOriginal = parseFloat(largoInput.dataset.original);
        if (isNaN(largoOriginal)) return;

        if (bastillaCheckbox.checked) {
            largoInput.value = (largoOriginal + 0.40).toFixed(2);
        } else {
            largoInput.value = largoOriginal.toFixed(2);
        }

        // Actualizar total de tela utilizada
        const event = new Event('input', { bubbles: true });
        largoInput.dispatchEvent(event);
    });

    // Script para agregar 65 cm de bastilla al largo del tergal
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'agregar_bastilla_tergal') {
            const largoTergalInput = document.getElementById('largo_tergal');
            const bastillaTergalCheckbox = e.target;

            if (!largoTergalInput || !bastillaTergalCheckbox) return;

            if (!largoTergalInput.dataset.original || largoTergalInput.value === "") {
                const original = parseFloat(largoTergalInput.value);
                if (!isNaN(original) && original > 0) {
                    largoTergalInput.dataset.original = original;
                }
            }

            const largoOriginal = parseFloat(largoTergalInput.dataset.original);
            if (isNaN(largoOriginal)) return;

            if (bastillaTergalCheckbox.checked) {
                largoTergalInput.value = (largoOriginal + 0.65).toFixed(2);
            } else {
                largoTergalInput.value = largoOriginal.toFixed(2);
            }

            const event = new Event('input', { bubbles: true });
            largoTergalInput.dispatchEvent(event);
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

    // Script para calcular el total de tela y tergal para la tabla de totales tergal y cortina y el costo de mano de obra
    document.addEventListener('input', function() {
        const noLienzosCortina = parseFloat(document.getElementById('no_lienzos_redondeado')?.value);
        const largoCortina = parseFloat(document.getElementById('largo')?.value);
        const precioTela = parseFloat(document.getElementById('precio_m2_tela')?.value);

        const noLienzosTergal = parseFloat(document.getElementById('no_lienzos_redondeado_tergal')?.value);
        const largoTergal = parseFloat(document.getElementById('largo_tergal')?.value);
        const precioTergal = parseFloat(document.getElementById('precio_m2_tergal')?.value);

        const totalTela = (!isNaN(noLienzosCortina) && !isNaN(largoCortina)) ? (noLienzosCortina * largoCortina) : 0;
        const totalTergal = (!isNaN(noLienzosTergal) && !isNaN(largoTergal)) ? (noLienzosTergal * largoTergal) : 0;

        const totalTelaFinal = (!isNaN(precioTela)) ? (totalTela * precioTela) : 0;
        const totalTergalFinal = (!isNaN(precioTergal)) ? (totalTergal * precioTergal) : 0;

        document.getElementById('total_tela').value = totalTela.toFixed(2);
        document.getElementById('total_tergal').value = totalTergal.toFixed(2);

        document.getElementById('total_tela_final').value = totalTelaFinal.toFixed(2);
        document.getElementById('total_tergal_final').value = totalTergalFinal.toFixed(2);

        document.getElementById('costo_total_tela_tergal').value = (totalTelaFinal + totalTergalFinal).toFixed(2);

        // Cálculo de Mano de Obra

        const m2CortinaInput = document.querySelector('[name="detalle[m2_1]"]');
        const m2TergalInput = document.querySelector('[name="detalle[m2_2]"]');

        const costoMO1 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_1]"]')?.value) || 0;
        const costoMO2 = parseFloat(document.querySelector('[name="detalle[costo_mano_obra_2]"]')?.value) || 0;

        const totalMO1 = document.querySelector('[name="detalle[total_mano_obra_1]"]');
        const totalMO2 = document.querySelector('[name="detalle[total_mano_obra_2]"]');
        const costoTotalMO = document.querySelector('[name="detalle[costo_total_mano_obra]"]');

        if (m2CortinaInput) m2CortinaInput.value = totalTela.toFixed(2);
        if (m2TergalInput) m2TergalInput.value = totalTergal.toFixed(2);

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
            const cantidadInput = fila.querySelector('input[name*="_cantidad"]');
            const precioInput = fila.querySelector('input[name*="_precio"]');

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
        const totalLienzos = totalLienzosCortina + totalLienzosTergal;
        document.getElementById('total_lienzos').value = totalLienzos > 0 ? totalLienzos : '';

        const totalForro = parseFloat(document.getElementById('total_forro')?.value) || 0;
        document.getElementById('total_m2_forro').value = totalForro > 0 ? totalForro.toFixed(2) : '';

        const totalTela = parseFloat(document.getElementById('total_tela')?.value) || 0;
        document.getElementById('total_m2_tela').value = totalTela > 0 ? totalTela.toFixed(2) : '';

        const totalTergal = parseFloat(document.getElementById('total_tergal')?.value) || 0;
        document.getElementById('total_m2_tergal').value = totalTergal > 0 ? totalTergal.toFixed(2) : '';

        // Cálculos monetarios de la tabla totales

        const costoTelaTergal = parseFloat(document.getElementById('costo_total_tela_tergal')?.value) || 0;
        const costoForro = parseFloat(document.querySelector('[name="detalle[total_final_forro]"]')?.value) || 0;
        const costoManoObra = parseFloat(document.querySelector('[name="detalle[costo_total_mano_obra]"]')?.value) || 0;
        const costoMateriales = parseFloat(document.getElementById('costo_total_materiales')?.value) || 0;

        const costoCortina = costoTelaTergal + costoForro + costoManoObra + costoMateriales;
        document.getElementById('costo_cortina').value = costoCortina > 0 ? costoCortina.toFixed(2) : '';

        const utilidad = costoCortina * 0.15;
        document.getElementById('utilidad').value = utilidad > 0 ? utilidad.toFixed(2) : '';

        const costoDecorador = costoCortina + utilidad;
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

    document.addEventListener('DOMContentLoaded', function() {
        setInterval(actualizarTablaTotales, 300);
    });

    document.addEventListener('input', function(e) {
        if (['ancho', 'ancho_tela'].includes(e.target.id)) {
            calcularLienzos();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const cortina = document.getElementById('cotinaCheck');
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
</script>
@endsection