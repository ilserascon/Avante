@extends('layouts.stisla')

@section('title', 'Inventario')

@section('content')
@php
    $formatearCantidad = function ($cantidad) {
        $formateado = number_format((float) $cantidad, 2, '.', '');

        return rtrim(rtrim($formateado, '0'), '.');
    };
@endphp
<style>
    .inventario-global-page .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .inventario-global-page .filter-card,
    .inventario-global-page .table-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
    }

    .inventario-global-page .table thead th {
        background: #f5f8ff;
        color: #4a5f83;
        font-weight: 700;
        border-bottom: 0;
        white-space: nowrap;
    }

    .inventario-global-page .table tbody td {
        vertical-align: middle;
    }

    .inventario-global-page .table tbody tr:hover td {
        background: #f8fbff;
    }

    .inventario-global-page .item-nombre {
        font-weight: 600;
        color: #1f3a69;
    }

    .inventario-global-page .cantidad-badge {
        display: inline-block;
        min-width: 2.5rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: #eef4ff;
        color: #1f3a69;
        font-weight: 700;
        text-align: center;
    }

    .inventario-global-page .tipo-badge {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .inventario-global-page .tipo-producto {
        background: #dbeafe;
        color: #1e40af;
    }

    .inventario-global-page .tipo-insumo {
        background: #dcfce7;
        color: #166534;
    }

    .inventario-global-page .almacen-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.65rem;
        margin: 0.15rem 0.35rem 0.15rem 0;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .inventario-global-page .almacen-chip strong {
        color: #1f3a69;
    }
</style>

<div class="section">
    <div class="inventario-global-page">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Inventario</h3>
                        <p class="text-muted mb-0">Existencia de productos e insumos en todos los almacenes.</p>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.inventario.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $nombre }}" placeholder="Buscar producto o insumo">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Tipo</label>
                            <select name="tipo" class="form-control">
                                <option value="">Productos e insumos</option>
                                <option value="producto" {{ $tipo === 'producto' ? 'selected' : '' }}>Productos</option>
                                <option value="insumo" {{ $tipo === 'insumo' ? 'selected' : '' }}>Insumos</option>
                            </select>
                        </div>
                        <div class="col-md-5 d-flex mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.inventario.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="section-body">
            <div class="card table-card">
                <div class="card-body table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>
                                <th class="text-right">Cantidad total</th>
                                <th>Almacenes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventario as $item)
                                <tr>
                                    <td>
                                        <span class="tipo-badge {{ $item['tipo'] === 'Producto' ? 'tipo-producto' : 'tipo-insumo' }}">
                                            {{ $item['tipo'] }}
                                        </span>
                                    </td>
                                    <td class="item-nombre">{{ $item['nombre'] }}</td>
                                    <td class="text-right">
                                        <span class="cantidad-badge">{{ $formatearCantidad($item['cantidad_total']) }}</span>
                                    </td>
                                    <td>
                                        @foreach($item['almacenes'] as $almacen)
                                            <span class="almacen-chip">
                                                {{ $almacen['nombre'] }}
                                                <strong>({{ $formatearCantidad($almacen['cantidad']) }})</strong>
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No hay existencias registradas con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $inventario->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
