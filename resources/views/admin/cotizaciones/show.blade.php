@extends('layouts.stisla')

@section('title', 'Detalle de Cotización')

@section('content')
@php
    $verDetalleTelaManoObra = auth()->user()?->veDetalleTelaManoObra() ?? false;
    $veUtilidad = auth()->user()?->veUtilidadCotizacion() ?? false;

    $fmtMoney = function ($value) {
        return '$' . number_format((float) ($value ?? 0), 2);
    };

    $fmtNum = function ($value, $decimals = 2) {
        return ($value !== null && $value !== '') ? number_format((float) $value, $decimals) : '-';
    };

    $fmtVal = function ($value) {
        return ($value !== null && $value !== '') ? $value : '-';
    };

    $estatus = strtolower((string) $cotizacion->estatus);
    $statusClass = in_array($estatus, ['solicitada', 'aceptada', 'rechazada', 'completada', 'cancelada']) ? 'status-' . $estatus : 'status-solicitada';

    $calcularCostoCortinaDetalle = function ($detalle) {
        $costo = (float) ($detalle->costo_cortina ?? 0);
        if ($costo > 0) {
            return $costo;
        }

        $materiales =
            ((float) ($detalle->cortinero_cantidad ?? 0) * (float) ($detalle->cortinero_precio ?? 0)) +
            ((float) ($detalle->cortinero_tergal_cantidad ?? 0) * (float) ($detalle->cortinero_tergal_precio ?? 0));

        return (float) ($detalle->costo_total_tela_tergal_forro ?? 0) +
            (float) ($detalle->costo_total_mano_obra ?? 0) +
            $materiales;
    };

    $totalInsumos = $cotizacion->insumos->sum(fn ($insumo) => (float) ($insumo->pivot->subtotal ?? 0));
    $totalProductos = $cotizacion->productos->sum(fn ($producto) => (float) ($producto->pivot->subtotal ?? 0));
    $totalDetalles = $detalles->sum(function ($detalle) use ($cotizacion, $calcularCostoCortinaDetalle) {
        $costo = $calcularCostoCortinaDetalle($detalle);
        $precio = $costo * 2;
        $descuento = (float) ($detalle->descuento ?? $cotizacion->descuento ?? 0);
        if ($descuento > 0) {
            $precio -= $precio * ($descuento / 100);
        }
        return $precio;
    });

    $subtotalGeneral = $totalDetalles + $totalInsumos + $totalProductos;
    $totalGeneral = $cotizacion->aplicar_iva ? $subtotalGeneral * 1.16 : $subtotalGeneral;

    $costoCortinaGlobal = $cotizacion->costo_cortina ?? $detalles->sum($calcularCostoCortinaDetalle);
    $costoDecoradorGlobal = $cotizacion->costo_decorador ?? null;
    $tieneInsumos = $cotizacion->insumos->isNotEmpty();
    $tieneProductos = $cotizacion->productos->isNotEmpty();
@endphp

<style>
    .cot-show .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .cot-show .info-card,
    .cot-show .section-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
        margin-bottom: 1.25rem;
    }

    .cot-show .section-card .card-header {
        background: #f5f8ff;
        border-bottom: 1px solid #e8eef8;
        padding: 0.9rem 1.25rem;
    }

    .cot-show .section-card .card-header h5,
    .cot-show .section-card .card-header h6 {
        margin: 0;
        color: #26344d;
        font-weight: 700;
    }

    .cot-show .section-card .card-header .text-muted {
        font-size: 0.82rem;
    }

    .cot-show .status-chip {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .cot-show .status-chip-lg {
        padding: 0.5rem 1.1rem;
        font-size: 0.9rem;
        letter-spacing: 0.4px;
        margin-top: 0.65rem;
    }

    .cot-show .hero-title-block h2 {
        font-weight: 700;
        color: #26344d;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .cot-show .hero-creado-por {
        margin-top: 0.85rem;
        font-size: 0.9rem;
        color: #6b7b95;
        font-weight: 500;
    }

    .cot-show .hero-creado-por i {
        color: #94a3b8;
        margin-right: 0.35rem;
    }

    .cot-show .hero-creado-por strong {
        color: #334155;
        font-weight: 600;
    }

    .cot-show .summary-breakdown {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-top: 1rem;
    }

    .cot-show .summary-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.75rem 1rem;
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 10px;
    }

    .cot-show .summary-line span {
        color: #6b7b95;
        font-size: 0.88rem;
        font-weight: 600;
    }

    .cot-show .summary-line strong {
        color: #26344d;
        font-size: 1rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .cot-show .status-solicitada { color: #8b5e00; background: #fff4d9; }
    .cot-show .status-aceptada { color: #166534; background: #dcfce7; }
    .cot-show .status-rechazada { color: #991b1b; background: #fee2e2; }
    .cot-show .status-completada { color: #1e40af; background: #dbeafe; }
    .cot-show .status-cancelada { color: #9a3412; background: #ffedd5; }

    .cot-show .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.35rem;
        margin-bottom: 0.35rem;
    }

    .cot-show .type-cortina { background: #e7f1ff; color: #1d4ed8; }
    .cot-show .type-tergal { background: #fff5df; color: #b45309; }
    .cot-show .type-forro { background: #f1efff; color: #5b3cc4; }

    .cot-show .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .cot-show .meta-item {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 12px;
        padding: 0.85rem 1rem;
    }

    .cot-show .meta-item .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7b95;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .cot-show .meta-item .value {
        color: #26344d;
        font-weight: 600;
        word-break: break-word;
    }

    .cot-show .data-table th {
        background: #f8fbff;
        color: #4a5f83;
        font-weight: 700;
        font-size: 0.82rem;
        white-space: nowrap;
    }

    .cot-show .data-table td {
        vertical-align: middle;
    }

    .cot-show .readonly-field {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.55rem 0.75rem;
        min-height: 38px;
        color: #334155;
        font-weight: 500;
    }

    .cot-show .readonly-field.money {
        font-weight: 700;
        color: #1f3a69;
    }

    .cot-show .summary-total {
        background: linear-gradient(135deg, #1f3a69 0%, #2d4a7c 100%);
        color: #fff;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
    }

    .cot-show .summary-total .amount {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: 0.3px;
    }

    .cot-show .detalle-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #6777ef;
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        margin-right: 0.5rem;
    }

    .cot-show .actions-bar .btn {
        border-radius: 10px;
    }
</style>

<div class="section cot-show">
    @if (session('error'))
        <div class="alert alert-danger">{!! session('error') !!}</div>
    @endif
    <div class="card hero-card mb-4">
        <div class="card-body py-4 px-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <div class="hero-title-block">
                    <h2>Cotización #{{ str_pad($cotizacion->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    <div>
                        <span class="status-chip status-chip-lg {{ $statusClass }}">{{ ucfirst($cotizacion->estatus) }}</span>
                    </div>
                    <div class="hero-creado-por">
                        Creado por: <strong>{{ $cotizacion->creadoPor?->name ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="actions-bar d-flex flex-wrap gap-2 mt-4 mt-lg-0">
                    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-light border">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>&nbsp;
                    @if(auth()->user()->puedeEditarCotizacion() && $cotizacion->estatus === 'solicitada')
                        <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="btn btn-warning text-white">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>&nbsp;
                    @endif
                    @if(!in_array($cotizacion->estatus, ['rechazada', 'cancelada']))
                        <a href="{{ route('admin.cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-danger" target="_blank">
                            <i class="fas fa-file-pdf mr-1"></i> PDF Cliente
                        </a>&nbsp;
                        @if(auth()->user()->puedeVerPdfDecorador())
                            <a href="{{ route('admin.cotizaciones.pdf-decorador', $cotizacion->id) }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-file-pdf mr-1"></i> PDF Decorador
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card info-card">
        <div class="card-body">
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="label">Cliente</div>
                    <div class="value">{{ $cotizacion->cliente?->nombre ?? 'N/A' }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">Fecha</div>
                    <div class="value">{{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">IVA</div>
                    <div class="value">{{ $cotizacion->aplicar_iva ? 'Aplicado (16%)' : 'No aplicado' }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">Precio total</div>
                    <div class="value">{{ $fmtMoney($cotizacion->precio_publico ?? $totalGeneral) }}</div>
                </div>
            </div>
        </div>
    </div>

    @forelse($detalles as $index => $detalle)
        @php
            $materialesDetalle =
                ((float) ($detalle->cortinero_cantidad ?? 0) * (float) ($detalle->cortinero_precio ?? 0)) +
                ((float) ($detalle->cortinero_tergal_cantidad ?? 0) * (float) ($detalle->cortinero_tergal_precio ?? 0));

            $totalLienzosDetalle = $detalle->total_lienzos;
            if ($totalLienzosDetalle === null || $totalLienzosDetalle === '') {
                $totalLienzosDetalle =
                    (float) ($detalle->no_lienzos_redondeado ?? 0) +
                    (float) ($detalle->no_lienzos_redondeado_tergal ?? 0) +
                    (float) ($detalle->no_lienzos_redondeado_forro ?? 0);
            }

            $totalM2TelaDetalle = $detalle->total_m2_tela ?? $detalle->total_tela;
            $totalM2TergalDetalle = $detalle->total_m2_tergal ?? $detalle->total_tergal;
            $totalM2ForroDetalle = $detalle->total_m2_forro ?? $detalle->total_forro;
            $costoCortinaDetalle = $calcularCostoCortinaDetalle($detalle);

            $utilidadDetalle = $costoCortinaDetalle * 0.15;
            $decoradorPct = (float) ($detalle->decorador_porcentaje ?? 15);
            $costoDecoradorDetalle = $costoCortinaDetalle * (1 + $decoradorPct / 100);
            $precioPublicoDetalle = $costoCortinaDetalle * 2;
            $descuentoDetalle = (float) ($detalle->descuento ?? $cotizacion->descuento ?? 0);
            if ($descuentoDetalle > 0) {
                $precioPublicoDetalle -= $precioPublicoDetalle * ($descuentoDetalle / 100);
            }
        @endphp

        <div class="card section-card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h5>
                            <span class="detalle-index">{{ $index + 1 }}</span>
                            {{ $detalle->descripcion }} 
                            <p class="text-muted mt-1" style="font-size: 1rem; margin-left: 40px;"> Área: {{ $fmtVal($detalle->area) }}</p>
                        </h5>
                        
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($detalle->lleva_cortina)
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Cortina</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="small text-muted font-weight-bold">Tela</label>
                                    <div class="readonly-field">{{ $detalle->tela?->nombre ?? $fmtVal($detalle->descripcion_tela) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Ancho tela (cm)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->ancho_tela) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Ancho cortina (cm)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->ancho) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Largo (m)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->largo) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Bastilla (m)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->bastilla) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">No. lienzos</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->no_lienzos) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Lienzos redondeados</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->no_lienzos_redondeado) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($detalle->lleva_tergal)
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Tergal</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="small text-muted font-weight-bold">Tergal</label>
                                    <div class="readonly-field">{{ $detalle->tergal?->nombre ?? $fmtVal($detalle->descripcion_tergal) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Ancho tela (cm)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->ancho_tergal) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Ancho tergal (cm)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->ancho_tergal_real) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Largo (m)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->largo_tergal) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Bastilla (m)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->bastilla_tergal) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">No. lienzos</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->no_lienzos_tergal) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Lienzos redondeados</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->no_lienzos_redondeado_tergal) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($detalle->lleva_forro)
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-3">Forro</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="small text-muted font-weight-bold">Forro</label>
                                    <div class="readonly-field">{{ $detalle->forro?->nombre ?? $fmtVal($detalle->descripcion_forro) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Ancho forro (cm)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->ancho_forro) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Ancho real (cm)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->ancho_forro_real) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Largo (m)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->largo_forro) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Bastilla (m)</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->bastilla_forro) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">No. lienzos</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->no_lienzos_forro) }}</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted font-weight-bold">Lienzos redondeados</label>
                                    <div class="readonly-field">{{ $fmtVal($detalle->no_lienzos_redondeado_forro) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($verDetalleTelaManoObra)
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <h6 class="font-weight-bold mb-2">Totales Tela, Tergal y Forro</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th>M²</th>
                                        <th>Precio m²</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cortina</td>
                                        <td>{{ $fmtNum($detalle->total_tela) }}</td>
                                        <td>{{ $fmtMoney($detalle->precio_m2_tela) }}</td>
                                        <td class="text-right">{{ $fmtMoney($detalle->total_tela_final) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tergal</td>
                                        <td>{{ $fmtNum($detalle->total_tergal) }}</td>
                                        <td>{{ $fmtMoney($detalle->precio_m2_tergal) }}</td>
                                        <td class="text-right">{{ $fmtMoney($detalle->total_tergal_final) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Forro</td>
                                        <td>{{ $fmtNum($detalle->total_forro) }}</td>
                                        <td>{{ $fmtMoney($detalle->precio_m2_forro) }}</td>
                                        <td class="text-right">{{ $fmtMoney($detalle->total_final_forro) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-right font-weight-bold">Costo total tela, tergal y forro</td>
                                        <td class="text-right font-weight-bold">{{ $fmtMoney($detalle->costo_total_tela_tergal_forro) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <h6 class="font-weight-bold mb-2">Mano de Obra</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th>m²</th>
                                        <th>Costo</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cortina</td>
                                        <td>{{ $fmtNum($detalle->m2_1) }}</td>
                                        <td>{{ $fmtMoney($detalle->costo_mano_obra_1) }}</td>
                                        <td class="text-right">{{ $fmtMoney($detalle->total_mano_obra_1) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tergal</td>
                                        <td>{{ $fmtNum($detalle->m2_2) }}</td>
                                        <td>{{ $fmtMoney($detalle->costo_mano_obra_2) }}</td>
                                        <td class="text-right">{{ $fmtMoney($detalle->total_mano_obra_2) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-right font-weight-bold">Costo total mano de obra</td>
                                        <td class="text-right font-weight-bold">{{ $fmtMoney($detalle->costo_total_mano_obra) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <h6 class="font-weight-bold mb-2">Materiales Varios</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Cantidad</th>
                                        <th>Precio unitario</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $detalle->cortinero?->nombre ?? 'Cortinero cortina' }}</td>
                                        <td>{{ $fmtNum($detalle->cortinero_cantidad) }}</td>
                                        <td>{{ $fmtMoney($detalle->cortinero_precio) }}</td>
                                        <td class="text-right">{{ $fmtMoney(((float) ($detalle->cortinero_cantidad ?? 0)) * ((float) ($detalle->cortinero_precio ?? 0))) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ $detalle->cortineroTergal?->nombre ?? 'Cortinero tergal' }}</td>
                                        <td>{{ $fmtNum($detalle->cortinero_tergal_cantidad) }}</td>
                                        <td>{{ $fmtMoney($detalle->cortinero_tergal_precio) }}</td>
                                        <td class="text-right">{{ $fmtMoney(((float) ($detalle->cortinero_tergal_cantidad ?? 0)) * ((float) ($detalle->cortinero_tergal_precio ?? 0))) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td colspan="3" class="text-right font-weight-bold">Costo total materiales</td>
                                        <td class="text-right font-weight-bold">{{ $fmtMoney($materialesDetalle) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <h6 class="font-weight-bold mb-2">Resumen del concepto</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered data-table mb-0">
                                <tbody>
                                    <tr>
                                        <th>Total No. lienzos</th>
                                        <td>{{ $fmtVal($totalLienzosDetalle) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total m² tela</th>
                                        <td>{{ $fmtNum($totalM2TelaDetalle) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total m² tergal</th>
                                        <td>{{ $fmtNum($totalM2TergalDetalle) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total m² forro</th>
                                        <td>{{ $fmtNum($totalM2ForroDetalle) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Costo cortina</th>
                                        <td class="money">{{ $fmtMoney($costoCortinaDetalle) }}</td>
                                    </tr>
                                    @if($veUtilidad)
                                        <tr>
                                            <th>Utilidad (15%)</th>
                                            <td>{{ $fmtMoney($utilidadDetalle) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Costo decorador ({{ $fmtNum($decoradorPct, 2) }}%)</th>
                                        <td>{{ $fmtMoney($costoDecoradorDetalle) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Descuento</th>
                                        <td>{{ $fmtNum($descuentoDetalle, 2) }}%</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Precio público</th>
                                        <td class="font-weight-bold">{{ $fmtMoney($precioPublicoDetalle) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        
    @endforelse

    @if($tieneInsumos)
    <div class="row">
        <div class="col-lg-12">
            <div class="card section-card">
                <div class="card-header">
                    <h5>Insumos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered data-table mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Insumo</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Descuento</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->insumos as $insumo)
                                    <tr>
                                        <td>{{ $insumo->tipoInsumo?->nombre ?? '-' }}</td>
                                        <td>{{ $insumo->nombre }}</td>
                                        <td>{{ $fmtNum($insumo->pivot->cantidad) }}</td>
                                        <td>{{ $fmtMoney($insumo->pivot->precio_unitario) }}</td>
                                        <td>{{ $fmtNum($insumo->pivot->descuento ?? 0, 2) }}%</td>
                                        <td class="text-right">{{ $fmtMoney($insumo->pivot->subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="text-right font-weight-bold">Total insumos</td>
                                    <td class="text-right font-weight-bold">{{ $fmtMoney($totalInsumos) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($tieneProductos)
    <div class="row">
        <div class="col-lg-12">
            <div class="card section-card">
                <div class="card-header">
                    <h5>Productos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered data-table mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Descuento</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->productos as $producto)
                                    <tr>
                                        <td>{{ $producto->tipoProducto?->nombre ?? '-' }}</td>
                                        <td>{{ $producto->nombre }}</td>
                                        <td>{{ $fmtNum($producto->pivot->cantidad) }}</td>
                                        <td>{{ $fmtMoney($producto->pivot->precio_unitario) }}</td>
                                        <td>{{ $fmtNum($producto->pivot->descuento ?? 0, 2) }}%</td>
                                        <td class="text-right">{{ $fmtMoney($producto->pivot->subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="text-right font-weight-bold">Total productos</td>
                                    <td class="text-right font-weight-bold">{{ $fmtMoney($totalProductos) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card section-card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h5 class="mb-0" style="color:#26344d;font-weight:700;">Resumen general</h5>
                    <div class="summary-breakdown">
                        <div class="summary-line">
                            <span>Total cortinas</span>
                            <strong>{{ $fmtMoney($totalDetalles) }}</strong>
                        </div>
                        <div class="summary-line">
                            <span>Total insumos</span>
                            <strong>{{ $fmtMoney($totalInsumos) }}</strong>
                        </div>
                        <div class="summary-line">
                            <span>Total productos</span>
                            <strong>{{ $fmtMoney($totalProductos) }}</strong>
                        </div>
                        <div class="summary-line">
                            <span>Costo decorador</span>
                            <strong>{{ $fmtMoney($costoDecoradorGlobal ?? $costoCortinaGlobal) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-4 mt-md-0">
                    <div class="summary-total text-center">
                        <div class="small text-uppercase" style="opacity:0.85;letter-spacing:0.5px;">Precio público</div>
                        <div class="amount">{{ $fmtMoney($totalGeneral) }}</div>
                        @if($cotizacion->aplicar_iva)
                            <div class="small mt-1" style="opacity:0.85;">Incluye IVA (16%)</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card section-card">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @php
                    $cliente = $cotizacion->cliente;
                    $telefonoPersonalWa = \App\Models\Cliente::normalizarTelefonoWhatsApp('6623165287');
                    $urlPdf = $cotizacion->shareUrl();
                    $mensajePersonalLinea = "Cotización para el Cliente: {$cliente->nombre}";
                    $mensajeCompartirLinea = "Hola {$cliente->nombre}, aquí puedes ver tu cotización:";
                @endphp
                <button type="button"
                    class="btn btn-success js-compartir-whatsapp-personal"
                    style="background-color:#4aa46b;border-color:#4aa46b;"
                    data-telefono="{{ $telefonoPersonalWa }}"
                    data-mensaje-linea="{{ e($mensajePersonalLinea) }}"
                    data-url-pdf="{{ $urlPdf }}">
                    <i class="fab fa-whatsapp mr-1"></i> Enviar a WhatsApp Personal
                </button>
                &nbsp;&nbsp;
                <button type="button"
                    class="btn btn-primary js-compartir-cotizacion"
                    data-mensaje-linea="{{ e($mensajeCompartirLinea) }}"
                    data-url-pdf="{{ $urlPdf }}">
                    <i class="fas fa-share-alt mr-1"></i> Compartir cotización
                </button>
                <small class="d-block text-muted mt-2">
                    Compartir cotización adjunta el PDF en móvil (WhatsApp, correo, etc.). WhatsApp Personal abre tu chat con el enlace y descarga el PDF.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pdfUrl = @json(route('admin.cotizaciones.pdf', $cotizacion));
    const pdfFileName = @json('cotizacion_' . $cotizacion->id . '.pdf');
    const cotizacionId = @json($cotizacion->id);

    async function obtenerPdfBlob() {
        const response = await fetch(pdfUrl, {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!response.ok) {
            throw new Error('No se pudo generar el PDF.');
        }

        return response.blob();
    }

    function descargarPdf(blob) {
        const downloadUrl = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = pdfFileName;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(downloadUrl);
    }

    async function ejecutarConPdf(btn, callback) {
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Preparando PDF...';

        try {
            const blob = await obtenerPdfBlob();
            await callback(blob);
        } catch (error) {
            alert(error.message || 'No se pudo compartir el PDF. Intenta de nuevo.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    document.querySelectorAll('.js-compartir-cotizacion').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const mensaje = (btn.dataset.mensajeLinea || '') + '\n' + (btn.dataset.urlPdf || '');

            ejecutarConPdf(btn, async function (blob) {
                const file = new File([blob], pdfFileName, { type: 'application/pdf' });

                if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        files: [file],
                        title: 'Cotización #' + cotizacionId,
                        text: mensaje.split('\n')[0] || 'Tu cotización',
                    });
                    return;
                }

                if (navigator.share) {
                    await navigator.share({
                        title: 'Cotización #' + cotizacionId,
                        text: mensaje,
                    });
                    return;
                }

                descargarPdf(blob);
                alert('PDF descargado. Compártelo desde tu carpeta de descargas o adjunta el archivo manualmente.');
            });
        });
    });

    document.querySelectorAll('.js-compartir-whatsapp-personal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const telefono = btn.dataset.telefono;
            const mensaje = (btn.dataset.mensajeLinea || '') + '\n' + (btn.dataset.urlPdf || '');

            ejecutarConPdf(btn, async function (blob) {
                descargarPdf(blob);
                window.open('https://wa.me/' + telefono + '?text=' + encodeURIComponent(mensaje), '_blank');
            });
        });
    });
});
</script>
@endsection
