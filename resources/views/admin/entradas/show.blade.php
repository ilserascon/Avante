@extends('layouts.stisla')

@section('title', 'Detalle de Entrada')

@section('content')
@php
    $totalItems = $entrada->detalles->count();
    $totalCantidad = $entrada->detalles->sum('cantidad');
@endphp

<style>
    .entrada-show .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .entrada-show .info-card,
    .entrada-show .section-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
        margin-bottom: 1.25rem;
    }

    .entrada-show .section-card .card-header {
        background: #f5f8ff;
        border-bottom: 1px solid #e8eef8;
        padding: 0.9rem 1.25rem;
    }

    .entrada-show .section-card .card-header h5 {
        margin: 0;
        color: #26344d;
        font-weight: 700;
    }

    .entrada-show .hero-title-block h2 {
        font-weight: 700;
        color: #26344d;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .entrada-show .hero-meta {
        margin-top: 0.85rem;
        font-size: 0.9rem;
        color: #6b7b95;
        font-weight: 500;
    }

    .entrada-show .hero-meta i {
        color: #94a3b8;
        margin-right: 0.35rem;
    }

    .entrada-show .hero-meta strong {
        color: #334155;
        font-weight: 600;
    }

    .entrada-show .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .entrada-show .meta-item {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 12px;
        padding: 0.85rem 1rem;
    }

    .entrada-show .meta-item .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7b95;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .entrada-show .meta-item .value {
        color: #26344d;
        font-weight: 600;
        word-break: break-word;
    }

    .entrada-show .data-table th {
        background: #f8fbff;
        color: #4a5f83;
        font-weight: 700;
        font-size: 0.82rem;
        white-space: nowrap;
    }

    .entrada-show .data-table td {
        vertical-align: middle;
    }

    .entrada-show .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .entrada-show .type-producto {
        background: #e7f1ff;
        color: #1d4ed8;
    }

    .entrada-show .type-insumo {
        background: #fff5df;
        color: #b45309;
    }

    .entrada-show .cantidad-badge {
        display: inline-block;
        min-width: 2.5rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: #eef4ff;
        color: #1f3a69;
        font-weight: 700;
        text-align: center;
    }

    .entrada-show .actions-bar .btn {
        border-radius: 10px;
    }
</style>

<div class="section entrada-show">
    <div class="card hero-card mb-4">
        <div class="card-body py-4 px-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                <div class="hero-title-block">
                    <h2>Entrada #{{ str_pad($entrada->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    <div class="hero-meta">
                        <i class="fas fa-user"></i> Registrada por: <strong>{{ $entrada->usuario->name ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="actions-bar d-flex flex-wrap gap-2 mt-4 mt-lg-0">
                    <a href="{{ route('admin.entradas.index') }}" class="btn btn-light border">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>&nbsp;
                    <a href="{{ route('admin.entradas.edit', $entrada->id) }}" class="btn btn-warning text-white">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card info-card">
        <div class="card-body">
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="label">Almacén</div>
                    <div class="value">{{ $entrada->almacen->nombre ?? 'N/A' }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">Fecha</div>
                    <div class="value">{{ $entrada->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">Total de líneas</div>
                    <div class="value">{{ $totalItems }}</div>
                </div>
                <div class="meta-item">
                    <div class="label">Cantidad total</div>
                    <div class="value">{{ number_format((float) $totalCantidad, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card section-card">
        <div class="card-header">
            <h5>Productos e insumos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered data-table mb-0">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th class="text-right">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entrada->detalles as $detalle)
                            <tr>
                                <td>
                                    @if($detalle->id_producto)
                                        <span class="type-badge type-producto">Producto</span>
                                    @else
                                        <span class="type-badge type-insumo">Insumo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detalle->id_producto)
                                        {{ $detalle->producto->nombre ?? '-' }}
                                    @else
                                        {{ $detalle->insumo->nombre_completo ?? $detalle->insumo->nombre ?? '-' }}
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="cantidad-badge">{{ number_format((float) $detalle->cantidad, 2) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No hay detalles registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
