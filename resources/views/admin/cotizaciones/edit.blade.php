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
            <div class="card">
                <div class="card-header">
                    <h4>Información General</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id">Cliente *</label>
                            <select name="cliente_id" id="cliente_id" class="form-control" required>
                                <option value="">Seleccione un cliente</option>
                                @foreach(\App\Models\Cliente::where('borrado', 0)->orderBy('nombre')->get() as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $cotizacion->cliente_id == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fecha">Fecha *</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" required 
                                   value="{{ $cotizacion->fecha }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="estatus">Estatus</label>
                            <select name="estatus" id="estatus" class="form-control">
                                <option value="solicitada" {{ $cotizacion->estatus == 'solicitada' ? 'selected' : '' }}>Solicitada</option>
                                <option value="aprobada" {{ $cotizacion->estatus == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                                <option value="rechazada" {{ $cotizacion->estatus == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                                <option value="completada" {{ $cotizacion->estatus == 'completada' ? 'selected' : '' }}>Completada</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tipos de Trabajo -->
            <div class="card">
                <div class="card-header">
                    <h4>Tipos de Trabajo</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="tipo[]" value="cortina" id="tipo_cortina" class="form-check-input"
                                       {{ $cotizacion->lleva_cortina ? 'checked' : '' }}>
                                <label for="tipo_cortina" class="form-check-label">Cortina</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="tipo[]" value="tergal" id="tipo_tergal" class="form-check-input"
                                       {{ $cotizacion->lleva_tergal ? 'checked' : '' }}>
                                <label for="tipo_tergal" class="form-check-label">Tergal</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="lleva_forro" value="1" id="lleva_forro" class="form-check-input"
                                       {{ $cotizacion->lleva_forro ? 'checked' : '' }}>
                                <label for="lleva_forro" class="form-check-label">Lleva Forro</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
let contadorOtrosInsumos = {{ $otrosInsumosExistentes->count() }};

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