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
                <!-- Aquí irán los campos que aparecen según selección -->
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
                        <tr>
                            <td><input type="number" name="detalle[total_tela_tergal]" class="form-control" step="0.01"></td>
                            <td><input type="number" name="detalle[precio_m2]" class="form-control" step="0.01"></td>
                            <td><input type="text" name="detalle[descripcion]" class="form-control"></td>
                            <td><input type="number" name="detalle[total]" class="form-control" step="0.01"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Costo total tela y tergal:</strong></td>
                            <td><input type="number" name="detalle[costo_total_tela_tergal]" class="form-control" step="0.01"></td>
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
                    <!-- Información General Tergal -->
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
                                <td><input type="number" name="detalle[total_forro]" class="form-control" step="0.01"></td>
                                <td><input type="text" name="detalle[descripcion_forro]" class="form-control"></td>
                                <td><input type="number" name="detalle[precio_m2_forro]" class="form-control" step="0.01"></td>
                                <td><input type="number" name="detalle[total_final_forro]" class="form-control" step="0.01"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Costo total forro:</strong></td>
                                <td><input type="number" name="detalle[costo_total_forro]" class="form-control" step="0.01"></td>
                            </tr>
                        </tbody>
                    </table>
                `;
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

        if (!largoInput.dataset.original) {
            const original = parseFloat(largoInput.value);
            if (!isNaN(original)) {
                largoInput.dataset.original = original;
            }
        }

        const largoOriginal = parseFloat(largoInput.dataset.original);
        if (isNaN(largoOriginal)) return;

        if (bastillaCheckbox.checked) {
            largoInput.value = (largoOriginal + 40).toFixed(2);
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

    document.addEventListener('input', function(e) {
        if (['ancho', 'ancho_tela'].includes(e.target.id)) {
            calcularLienzos();
        }
    });
    
</script>
@endsection