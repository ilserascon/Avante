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
                                value="{{ old('detalle.no_lienzos', $detalleCotizacion->no_lienzos ?? '') }}">
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

            <!-- Script para cálculos automáticos (opcional) -->
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

            <!-- Insumos Fijos -->
            <div class="card">
                <div class="card-header">
                    <h4>Insumos Fijos</h4>
                </div>
                <div class="card-body">
                    @php
                    $insumosFijosData = ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'];
                    $insumosRelacionados = $cotizacion->insumos->keyBy('nombre');
                    @endphp

                    @foreach($insumosFijosData as $nombreInsumo)
                    @php
                    $insumoFijo = $insumosFijos->get($nombreInsumo);
                    $insumoRelacionado = $insumosRelacionados->get($nombreInsumo);
                    $cantidad = $insumoRelacionado ? $insumoRelacionado->pivot->cantidad : 0;
                    @endphp
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-6">
                            <label>{{ $nombreInsumo }}</label>
                            <input type="text" class="form-control" value="{{ $nombreInsumo }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="{{ strtolower($nombreInsumo) }}_cantidad">Cantidad</label>
                            <input type="number" name="detalle[{{ strtolower($nombreInsumo) }}_cantidad]"
                                id="{{ strtolower($nombreInsumo) }}_cantidad" class="form-control"
                                min="0" step="0.01" value="{{ $cantidad }}">
                        </div>
                        <div class="col-md-3">
                            <label>Precio Unitario</label>
                            <input type="text" class="form-control"
                                value="${{ $insumoFijo ? number_format($insumoFijo->precio_publico, 2) : '0.00' }}" readonly>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Otros Insumos -->
            <div class="card">
                <div class="card-header">
                    <h4>Otros Insumos</h4>
                    <div class="card-header-action">
                        <button type="button" class="btn btn-success btn-sm" onclick="agregarOtroInsumo()">
                            <i class="fas fa-plus"></i> Agregar Insumo
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="otros-insumos-container">
                        @php
                        $otrosInsumosExistentes = $cotizacion->insumos->whereNotIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas']);
                        $contador = 0;
                        @endphp

                        @foreach($otrosInsumosExistentes as $insumoExistente)
                        @php $contador++; @endphp
                        <div class="row mb-3 align-items-end otro-insumo-row">
                            <div class="col-md-5">
                                <label for="otros{{ $contador }}_nombre">Insumo</label>
                                <select name="detalle[otros{{ $contador }}_nombre]" id="otros{{ $contador }}_nombre" class="form-control">
                                    <option value="">Seleccionar insumo...</option>
                                    @foreach($insumos as $insumo)
                                    <option value="{{ $insumo->id }}" {{ $insumoExistente->id == $insumo->id ? 'selected' : '' }}>
                                        {{ $insumo->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="otros{{ $contador }}_cantidad">Cantidad</label>
                                <input type="number" name="detalle[otros{{ $contador }}_cantidad]"
                                    id="otros{{ $contador }}_cantidad" class="form-control"
                                    min="0" step="0.01" value="{{ $insumoExistente->pivot->cantidad }}">
                            </div>
                            <div class="col-md-3">
                                <label for="otros{{ $contador }}_precio">Precio Unitario</label>
                                <input type="number" name="detalle[otros{{ $contador }}_precio]"
                                    id="otros{{ $contador }}_precio" class="form-control"
                                    min="0" step="0.01" value="{{ $insumoExistente->pivot->precio_unitario }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarInsumo(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

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