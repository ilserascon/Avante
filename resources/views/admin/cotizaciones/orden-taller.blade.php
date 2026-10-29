@extends('layouts.stisla')

@section('title', 'Orden de taller')

@section('content')
@php
    $fmtVal = function ($value) {
        return ($value !== null && $value !== '') ? $value : '—';
    };

    $largoFabricacion = function ($largo, $bastillaCm) {
        if ($largo === null || $largo === '') {
            return '—';
        }

        $metros = (float) $largo;
        if ($bastillaCm !== null && $bastillaCm !== '') {
            $metros -= ((float) $bastillaCm) / 100;
        }
        if ($metros < 0) {
            $metros = 0;
        }

        return rtrim(rtrim(number_format($metros, 3, '.', ''), '0'), '.');
    };

    $anchoNominal = function ($value) {
        if ($value === null || $value === '') {
            return '—';
        }

        $metros = (float) $value <= 10 ? (float) $value : (float) $value / 100;

        return rtrim(rtrim(number_format($metros, 3, '.', ''), '0'), '.');
    };

    $campo = function ($index, $detalle, $field) {
        if (is_array(old('partidas'))) {
            return old("partidas.$index.$field");
        }

        $value = $detalle->{$field};

        return $value === null ? '' : $value;
    };

    $bastillaCampo = function ($index, $detalle, $field, $sugerida) use ($campo) {
        $valor = $campo($index, $detalle, $field);
        if ($valor === '' && !is_array(old('partidas')) && $sugerida !== null) {
            return rtrim(rtrim(number_format((float) $sugerida, 2, '.', ''), '0'), '.');
        }

        return $valor;
    };

    $totalPartidas = $detalles->count();
@endphp

<style>
    .orden-taller .hero-card,
    .orden-taller .section-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
        margin-bottom: 1.25rem;
    }

    .orden-taller .hero-card {
        background: linear-gradient(135deg, #f7f9fd 0%, #eef3fb 100%);
    }

    .orden-taller .section-card .card-header {
        background: #f5f8ff;
        border-bottom: 1px solid #e8eef8;
        padding: 0.9rem 1.25rem;
    }

    .orden-taller .section-card .card-header h5 {
        margin: 0;
        color: #26344d;
        font-weight: 700;
    }

    .orden-taller .readonly-field {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.55rem 0.75rem;
        min-height: 38px;
        color: #334155;
        font-weight: 500;
    }

    .orden-taller .bloque-titulo {
        font-weight: 700;
        color: #26344d;
        margin-bottom: 0.85rem;
    }

    .orden-taller label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.3rem;
    }
</style>

<div class="section orden-taller">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card hero-card mb-4">
        <div class="card-body py-4 px-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <div>
                    <h2 class="mb-1" style="font-weight: 700; color: #26344d;">Orden de taller</h2>
                    <div class="text-muted">
                        Cotización #{{ str_pad($cotizacion->id, 5, '0', STR_PAD_LEFT) }}
                        · {{ $cotizacion->cliente?->nombre ?? 'Sin cliente' }}
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0">
                    <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-light border">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a la cotización
                    </a>&nbsp;
                    <a href="{{ route('admin.cotizaciones.orden-taller.pdf', $cotizacion->id) }}" class="btn btn-dark" target="_blank">
                        <i class="fas fa-file-pdf mr-1"></i> Ver PDF guardado
                    </a>
                </div>
            </div>
            <p class="text-muted mb-0 mt-3">
                Lo que ya viene de la cotización se muestra fijo. Lo demás se captura aquí. Los campos vacíos se guardan en blanco.
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.cotizaciones.orden-taller.guardar', $cotizacion->id) }}">
        @csrf

        @foreach($detalles as $index => $detalle)
            <div class="card section-card">
                <div class="card-header">
                    <h5>
                        Partida {{ $index + 1 }} de {{ $totalPartidas }}
                        <span class="text-muted font-weight-normal">· {{ $fmtVal($detalle->area) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <input type="hidden" name="partidas[{{ $index }}][id]" value="{{ $detalle->id }}">

                    @php
                        $baston = $bastonesPorDetalle[$detalle->id] ?? ['numero' => null, 'medida' => null];
                        $bastillaInfo = $bastillas[$detalle->id] ?? ['codigo' => null, 'sugerida' => null, 'efectiva' => null, 'tergal' => ['codigo' => null, 'sugerida' => null]];
                        $bastillaTergalInfo = $bastillaInfo['tergal'] ?? ['codigo' => null, 'sugerida' => null];
                        $bastillaValor = $bastillaCampo($index, $detalle, 'bastilla_taller', $bastillaInfo['sugerida'] ?? null);
                        $bastillaTergalValor = $bastillaCampo($index, $detalle, 'bastilla_taller_tergal', $bastillaTergalInfo['sugerida'] ?? null);
                        $largoFab = $largoFabricacion($detalle->largo, $bastillaValor);
                        $largoFabTergal = $largoFabricacion($detalle->largo_tergal, $bastillaTergalValor);
                    @endphp
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Área</label>
                            <div class="readonly-field">{{ $fmtVal($detalle->area) }}</div>
                        </div>
                    </div>

                    @if($detalle->lleva_cortina)
                    <h6 class="bloque-titulo">Cortina</h6>
                    <div class="row mb-2">
                        <div class="col-md-2 mb-3">
                            <label for="bastilla_taller_{{ $detalle->id }}">Bastilla (cm)</label>
                            <input id="bastilla_taller_{{ $detalle->id }}" type="number" min="0" step="0.01" class="form-control js-bastilla" data-medida="medida_fab_{{ $detalle->id }}" name="partidas[{{ $index }}][bastilla_taller]" value="{{ $bastillaValor }}">
                            @if($bastillaInfo['codigo'])
                                <small class="text-muted">Cortinero {{ $bastillaInfo['codigo'] }}. Puedes cambiar el descuento.</small>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Medida de fabricación</label>
                            <div class="readonly-field js-medida-fab" id="medida_fab_{{ $detalle->id }}" data-ancho="{{ $anchoNominal($detalle->ancho) }}" data-largo="{{ $detalle->largo }}">{{ $anchoNominal($detalle->ancho) }} × {{ $largoFab }} m</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Tela cortina</label>
                            <div class="readonly-field">{{ $fmtVal($detalle->tela?->nombre ?: $detalle->descripcion_tela) }}</div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Color cortina</label>
                            <div class="readonly-field">{{ $fmtVal($detalle->tela?->color) }}</div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Cortinero cortina</label>
                            <div class="readonly-field">{{ $fmtVal($detalle->cortinero?->nombre) }}</div>
                        </div>
                    </div>
                    @endif

                    @if($detalle->lleva_tergal)
                        <h6 class="bloque-titulo">Tergal</h6>
                        <div class="row mb-2">
                            <div class="col-md-2 mb-3">
                                <label for="bastilla_taller_tergal_{{ $detalle->id }}">Bastilla (cm)</label>
                                <input id="bastilla_taller_tergal_{{ $detalle->id }}" type="number" min="0" step="0.01" class="form-control js-bastilla" data-medida="medida_fab_tergal_{{ $detalle->id }}" name="partidas[{{ $index }}][bastilla_taller_tergal]" value="{{ $bastillaTergalValor }}">
                                @if($bastillaTergalInfo['codigo'])
                                    <small class="text-muted">Cortinero {{ $bastillaTergalInfo['codigo'] }}. Puedes cambiar el descuento.</small>
                                @endif
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Medida de fabricación</label>
                                <div class="readonly-field js-medida-fab" id="medida_fab_tergal_{{ $detalle->id }}" data-ancho="{{ $anchoNominal($detalle->ancho_tergal_real) }}" data-largo="{{ $detalle->largo_tergal }}">{{ $anchoNominal($detalle->ancho_tergal_real) }} × {{ $largoFabTergal }} m</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Tela tergal</label>
                                <div class="readonly-field">{{ $fmtVal($detalle->tergal?->nombre ?: $detalle->descripcion_tergal) }}</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Color tergal</label>
                                <div class="readonly-field">{{ $fmtVal($detalle->tergal?->color) }}</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>Cortinero tergal</label>
                                <div class="readonly-field">{{ $fmtVal($detalle->cortineroTergal?->nombre) }}</div>
                            </div>
                        </div>
                    @endif

                    <h6 class="bloque-titulo">Datos de taller</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="techo_muro_{{ $detalle->id }}">Techo / muro</label>
                            <select id="techo_muro_{{ $detalle->id }}" name="partidas[{{ $index }}][techo_muro]" class="form-control">
                                <option value="">Selecciona</option>
                                <option value="Piso Techo" @selected($campo($index, $detalle, 'techo_muro') === 'Piso Techo')>Piso techo</option>
                                <option value="Muro" @selected($campo($index, $detalle, 'techo_muro') === 'Muro')>Muro</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="confeccion_{{ $detalle->id }}">Confección</label>
                            <input id="confeccion_{{ $detalle->id }}" type="text" maxlength="255" class="form-control" name="partidas[{{ $index }}][confeccion]" value="{{ $campo($index, $detalle, 'confeccion') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="apertura_{{ $detalle->id }}">Apertura</label>
                            <select id="apertura_{{ $detalle->id }}" name="partidas[{{ $index }}][apertura]" class="form-control">
                                <option value="">Selecciona</option>
                                <option value="Central" @selected($campo($index, $detalle, 'apertura') === 'Central')>Central</option>
                                <option value="Jalón izquierdo" @selected(in_array($campo($index, $detalle, 'apertura'), ['Jalón izquierdo', 'Izquierdo'], true))>Jalón izquierdo</option>
                                <option value="Jalón derecho" @selected(in_array($campo($index, $detalle, 'apertura'), ['Jalón derecho', 'Derecho'], true))>Jalón derecho</option>
                                <option value="Híbrido" @selected($campo($index, $detalle, 'apertura') === 'Híbrido')>Híbrido</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="corredera_{{ $detalle->id }}">Corredera</label>
                            <select id="corredera_{{ $detalle->id }}" name="partidas[{{ $index }}][corredera_id]" class="form-control">
                                <option value="">Selecciona</option>
                                @foreach($correderas as $corredera)
                                    <option value="{{ $corredera->id }}" @selected((string) $campo($index, $detalle, 'corredera_id') === (string) $corredera->id)>
                                        {{ $corredera->nombre }} — {{ $corredera->primeraMedidaMostrar() }} cm
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cinta_{{ $detalle->id }}">Cinta</label>
                            <select id="cinta_{{ $detalle->id }}" name="partidas[{{ $index }}][cinta_id]" class="form-control">
                                <option value="">Selecciona</option>
                                @foreach($cintas as $cinta)
                                    <option value="{{ $cinta->id }}" @selected((string) $campo($index, $detalle, 'cinta_id') === (string) $cinta->id)>
                                        {{ $cinta->nombre }} — {{ $cinta->primeraMedidaMostrar() }} cm
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Alturas originales (referencia)</label>
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">Izq.</th>
                                        <th class="text-center">Centro</th>
                                        <th class="text-center">Derecho</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input id="altura_izq_{{ $detalle->id }}" type="number" min="0" step="0.001" class="form-control" name="partidas[{{ $index }}][altura_izq]" value="{{ $campo($index, $detalle, 'altura_izq') }}">
                                        </td>
                                        <td>
                                            <input id="altura_centro_{{ $detalle->id }}" type="number" min="0" step="0.001" class="form-control" name="partidas[{{ $index }}][altura_centro]" value="{{ $campo($index, $detalle, 'altura_centro') }}">
                                        </td>
                                        <td>
                                            <input id="altura_der_{{ $detalle->id }}" type="number" min="0" step="0.001" class="form-control" name="partidas[{{ $index }}][altura_der]" value="{{ $campo($index, $detalle, 'altura_der') }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="vuelta_lateral_{{ $detalle->id }}">Vuelta lateral (cm)</label>
                            <input id="vuelta_lateral_{{ $detalle->id }}" type="number" min="0" step="0.001" class="form-control" name="partidas[{{ $index }}][vuelta_lateral]" value="{{ $campo($index, $detalle, 'vuelta_lateral') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="control_{{ $detalle->id }}">Control cortina</label>
                            <input id="control_{{ $detalle->id }}" type="text" maxlength="100" class="form-control" name="partidas[{{ $index }}][control]" value="{{ $campo($index, $detalle, 'control') }}">
                        </div>
                        <div class="w-100"></div>
                        <div class="col-md-3 mb-3">
                            <label>Número de bastones</label>
                            <div class="readonly-field">{{ $fmtVal($baston['numero']) }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Medida de bastón</label>
                            <div class="readonly-field">{{ $fmtVal($baston['medida']) }}</div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="observacion_taller_{{ $detalle->id }}">Observación</label>
                            <textarea id="observacion_taller_{{ $detalle->id }}" maxlength="2000" rows="3" class="form-control" name="partidas[{{ $index }}][observacion_taller]">{{ $campo($index, $detalle, 'observacion_taller') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" name="despues" value="guardar" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Guardar datos de taller
        </button>
        <button type="submit" name="despues" value="pdf" class="btn btn-dark">
            <i class="fas fa-file-pdf mr-1"></i> Guardar y ver PDF
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.js-bastilla').forEach(function (input) {
    const actualizar = function () {
        const medida = document.getElementById(input.dataset.medida);
        if (!medida) {
            return;
        }
        const ancho = medida.dataset.ancho || '—';
        let largo = parseFloat(medida.dataset.largo);
        const bastilla = parseFloat(input.value);
        if (!Number.isNaN(largo) && !Number.isNaN(bastilla)) {
            largo -= bastilla / 100;
            if (largo < 0) {
                largo = 0;
            }
            largo = String(Math.round(largo * 1000) / 1000);
        } else if (Number.isNaN(largo)) {
            largo = '—';
        }
        medida.textContent = ancho + ' × ' + largo + ' m';
    };
    input.addEventListener('input', actualizar);
});
</script>
@endsection
