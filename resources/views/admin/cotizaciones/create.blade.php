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

            <div class="form-group">
                <label>Tipo de Cotización</label><br>
                <div class="form-check form-check-inline">
                    <input type="checkbox" id="cotinaCheck" name="tipo[]" value="cortina" class="form-check-input">
                    <label class="form-check-label" for="cotinaCheck">Cortina</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" id="tergalCheck" name="tipo[]" value="tergal" class="form-check-input">
                    <label class="form-check-label" for="tergalCheck">Tergal</label>
                </div>
            </div>

            <div class="form-group">
                <input type="checkbox" id="forroCheck" name="lleva_forro" value="1">
                <label for="forroCheck">Lleva Forro</label>
            </div>

            <div id="form-dinamico">
                <!-- Formularios dinámicos -->
            </div>

            <div id="tabla-totales-tela-tergal" class="mt-4">
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
                                    <input type="number" name="detalle[precio_m2_tela]" id="precio_m2_tela" class="form-control" step="0.01">
                                </div>
                            </td>
                            <td>
                                <input type="text" name="detalle[descripcion_tela]" class="form-control" placeholder="Cortina">
                            </td>
                            <td>
                                <input type="number" name="detalle[total_tela_final]" id="total_tela_final" class="form-control" step="0.01">
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
                                    <input type="number" name="detalle[precio_m2_tergal]" id="precio_m2_tergal" class="form-control" step="0.01">
                                </div>
                            </td>
                            <td>
                                <input type="text" name="detalle[descripcion_tergal]" class="form-control" placeholder="Tergal">
                            </td>
                            <td>
                                <input type="number" name="detalle[total_tergal_final]" id="total_tergal_final" class="form-control" step="0.01">
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


            <div id="tabla-mano-obra" class="mt-4">
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
                            <td><input type="number" name="detalle[costo_mano_obra_1]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="detalle[total_mano_obra_1]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td><input type="number" name="detalle[m2_2]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="detalle[costo_mano_obra_2]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="detalle[total_mano_obra_2]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end"><strong>Costo Mano de Obra:</strong></td>
                            <td><input type="number" name="detalle[costo_total_mano_obra]" class="form-control" step="0.01"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="tabla-materiales-varios" class="mt-4">
                <table class="table table-bordered mt-4">
                    <thead class="table-light">
                        <tr>
                            <th>Materiales Varios</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ojillos</td>
                            <td><input type="number" name="detalle[ojillos_cantidad]" class="form-control" step="1" value="20"></td>
                            <td><input type="number" name="detalle[ojillos_precio]" class="form-control" step="0.01" value="15.00"></td>
                        </tr>
                        <tr>
                            <td>Cortinero</td>
                            <td><input type="number" name="detalle[cortinero_cantidad]" class="form-control" step="1" value="1"></td>
                            <td><input type="number" name="detalle[cortinero_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td>Puntas</td>
                            <td><input type="number" name="detalle[puntas_cantidad]" class="form-control" step="1" value="2"></td>
                            <td><input type="number" name="detalle[puntas_precio]" class="form-control" step="0.01" value="100.00"></td>
                        </tr>
                        <tr>
                            <td>Mensulas</td>
                            <td><input type="number" name="detalle[mensulas_cantidad]" class="form-control" step="1"></td>
                            <td><input type="number" name="detalle[mensulas_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td>Otros</td>
                            <td><input type="number" name="detalle[otros1_cantidad]" class="form-control" step="1"></td>
                            <td><input type="number" name="detalle[otros1_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td>Otros</td>
                            <td><input type="number" name="detalle[otros2_cantidad]" class="form-control" step="1"></td>
                            <td><input type="number" name="detalle[otros2_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td>Otros</td>
                            <td><input type="number" name="detalle[otros3_cantidad]" class="form-control" step="1"></td>
                            <td><input type="number" name="detalle[otros3_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td>Otros</td>
                            <td><input type="number" name="detalle[otros4_cantidad]" class="form-control" step="1"></td>
                            <td><input type="number" name="detalle[otros4_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td>Otros</td>
                            <td><input type="number" name="detalle[otros5_cantidad]" class="form-control" step="1"></td>
                            <td><input type="number" name="detalle[otros5_precio]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end"><strong>Costo Total Materiales:</strong></td>
                            <td><input type="number" name="detalle[costo_total_materiales]" class="form-control" step="0.01"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <label>Total</label>
                <input type="number" name="total" class="form-control" required step="0.01">
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cotización</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cortina = document.getElementById('cotinaCheck');
        const tergal = document.getElementById('tergalCheck');
        const forro = document.getElementById('forroCheck');
        const formDinamico = document.getElementById('form-dinamico');

        function actualizarFormulario() {
            // Guardar valores
            const valoresPrevios = {};
            const inputsActuales = formDinamico.querySelectorAll('input');
            inputsActuales.forEach(input => {
                if (input.name) {
                    valoresPrevios[input.name] = input.value;
                }
            });

            formDinamico.innerHTML = '';

            if (cortina.checked) {
                formDinamico.innerHTML += `
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">Información General Cortina</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="ancho_tela">Ancho de tela</label></td>
                                <td><input type="text" name="detalle[ancho_tela]" id="ancho_tela" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="ancho">Ancho</label></td>
                                <td><input type="text" name="detalle[ancho]" id="ancho" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="largo">Largo</label></td>
                                <td>
                                    <div class="d-flex">
                                        <input type="text" name="detalle[largo]" id="largo" class="form-control" />
                                        <input type="checkbox" id="agregar_bastilla" class="ms-3" style="margin-left: 10px;">
                                        <label for="agregar_bastilla" class="ms-3" style="margin-left: 10px;">Agregar 40 cm de Bastilla</label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="no_lienzos">No. Lienzos</label></td>
                                <td><input type="number" name="detalle[no_lienzos]" id="no_lienzos" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="no_lienzos_redondeado">No. Lienzos Redondeados</label></td>
                                <td><input type="number" name="detalle[no_lienzos_redondeado]" id="no_lienzos_redondeado" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                `;
            }

            if (tergal.checked) {
                formDinamico.innerHTML += `
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">Información General Tergal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label for="ancho_tergal">Ancho de tergal</label></td>
                                <td><input type="text" name="detalle[ancho_tergal]" id="ancho_tergal" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="ancho_tergal_real">Ancho</label></td>
                                <td><input type="text" name="detalle[ancho_tergal_real]" id="ancho_tergal_real" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="largo_tergal">Largo</label></td>
                                <td><input type="text" name="detalle[largo_tergal]" id="largo_tergal" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="no_lienzos_tergal">No. Lienzos</label></td>
                                <td><input type="number" name="detalle[no_lienzos_tergal]" id="no_lienzos_tergal" class="form-control"></td>
                            </tr>
                            <tr>
                                <td><label for="no_lienzos_redondeado_tergal">No. Lienzos Redondeados</label></td>
                                <td><input type="number" name="detalle[no_lienzos_redondeado_tergal]" id="no_lienzos_redondeado_tergal" class="form-control"></td>
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
                        if (anchoCortina?.value && largoCortina?.value && anchoTelaCortina?.value) {
                            anchoTergal.value = anchoCortina.value;
                            largoTergal.value = (parseFloat(largoCortina.value) + 0.65).toFixed(2);
                            anchoTelaTergal.value = anchoTelaCortina.value;

                            /* // Hacer campos readonly
                            anchoTergal.readOnly = true;
                            largoTergal.readOnly = true;
                            anchoTelaTergal.readOnly = true; */

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
                                <th>Descripción</th>
                                <th>Precio m²</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" id="total_forro" name="detalle[total_forro]" class="form-control" step="0.01"></td>
                                <td><input type="text" name="detalle[descripcion_forro]" class="form-control"></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="double" name="detalle[precio_m2_forro]" id="precio_m2_forro" class="form-control" step="0.01">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="double" name="detalle[total_final_forro]" class="form-control" step="0.01">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Costo total forro:</strong></td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="double" name="detalle[costo_total_forro]" class="form-control" step="0.01">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `;

                setTimeout(() => {
                    const anchoTela = document.getElementById('ancho_tela');
                    const totalForro = document.getElementById('total_forro');
                    const precioM2 = document.querySelector('[name="detalle[precio_m2_forro]"]');
                    const totalFinal = document.querySelector('[name="detalle[total_final_forro]"]');
                    const costoTotal = document.querySelector('[name="detalle[costo_total_forro]"]');

                    // Copiar ancho de tela si existe
                    if (anchoTela && totalForro && anchoTela.value) {
                        const valor = parseFloat(anchoTela.value);
                        if (!isNaN(valor)) {
                            totalForro.value = valor.toFixed(2);
                            totalForro.dataset.original = valor;
                        }
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
                }, 0);

            }

            // Restaura valores guardados
            const nuevosInputs = formDinamico.querySelectorAll('input');
            nuevosInputs.forEach(input => {
                if (input.name && valoresPrevios.hasOwnProperty(input.name)) {
                    input.value = valoresPrevios[input.name];
                }
            });
        }


        cortina.addEventListener('change', actualizarFormulario);
        tergal.addEventListener('change', actualizarFormulario);
        forro.addEventListener('change', actualizarFormulario);
    });

    // Script para agregar 40 cm de bastilla al largo
    document.addEventListener('change', function() {
        const largoInput = document.getElementById('largo');
        const bastillaCheckbox = document.getElementById('agregar_bastilla');

        if (!largoInput.value) return;

        // Actualiza el valor original cuando el usuario cambia manualmente el valor de largo
        if (!largoInput.dataset.original || largoInput.value != largoInput.dataset.original) {
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

    // Script para calcular el total de tela y tergal para la tabla de totales tergal y cortina
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
    });

    document.addEventListener('input', function(e) {
        if (['ancho', 'ancho_tela'].includes(e.target.id)) {
            calcularLienzos();
        }
    });
</script>
@endsection