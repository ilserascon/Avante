@extends('layouts.stisla')

@section('title', 'Inventario — ' . $almacen->nombre)

@section('content')
<style>
    .inventario-page .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .inventario-page .hero-card h1 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #26344d;
    }

    .inventario-page .hero-subtitle {
        color: #6b7b95;
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    .inventario-page .filter-card,
    .inventario-page .table-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
    }

    .inventario-page .filter-card .card-body {
        padding: 1rem 1.25rem;
    }

    .inventario-page .table thead th {
        background: #f5f8ff;
        color: #4a5f83;
        font-weight: 700;
        border-bottom: 0;
        white-space: nowrap;
    }

    .inventario-page .table tbody td {
        vertical-align: middle;
    }

    .inventario-page .table tbody tr {
        transition: all .25s ease;
    }

    .inventario-page .table tbody tr:hover {
        background: #f8fbff;
    }

    .inventario-page .table tbody tr:hover td {
        color: #243b63;
        background: #f8fbff;
    }

    .inventario-page .item-nombre {
        font-weight: 600;
        color: #1f3a69;
    }

    .inventario-page .cantidad-badge {
        display: inline-block;
        min-width: 2.5rem;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: #eef4ff;
        color: #1f3a69;
        font-weight: 700;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .inventario-page .hero-actions {
            margin-top: 0.75rem;
            width: 100%;
        }

        .inventario-page .hero-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="section">
    <div class="inventario-page">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Inventario - {{ $almacen->nombre }}</h3>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.almacenes.index') }}" class="btn btn-light border px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.almacenes.existencia', $almacen->id) }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Mostrar</label>
                            <select name="tipo" class="form-control">
                                <option value="">Seleccione...</option>
                                <option value="producto" {{ request('tipo') == 'producto' ? 'selected' : '' }}>Productos</option>
                                <option value="insumo" {{ request('tipo') == 'insumo' ? 'selected' : '' }}>Insumos</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Producto</label>
                            <input type="text" name="producto" class="form-control" value="{{ request('producto') }}" placeholder="Nombre del producto">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Insumo</label>
                            <input type="text" name="insumo" class="form-control" value="{{ request('insumo') }}" placeholder="Nombre del insumo">
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.almacenes.existencia', $almacen->id) }}" class="btn btn-light border flex-grow-1">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="section-body">
            <div class="card table-card">
                <div class="card-body table-responsive">
                    @if(request('tipo') == 'producto')
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($existencias as $fila)
                                    @if(isset($fila['producto']) && $fila['producto'] !== '-')
                                        <tr>
                                            <td class="item-nombre">{{ $fila['producto'] }}</td>
                                            <td class="text-right"><span class="cantidad-badge">{{ $fila['cantidad_producto'] }}</span></td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">No hay productos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @elseif(request('tipo') == 'insumo')
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Insumo</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($existencias as $fila)
                                    @if(isset($fila['insumo']) && $fila['insumo'] !== '-')
                                        <tr>
                                            <td class="item-nombre">{{ $fila['insumo'] }}</td>
                                            <td class="text-right"><span class="cantidad-badge">{{ $fila['cantidad_insumo'] }}</span></td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">No hay insumos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-info mb-0">
                            Selecciona una tabla para ver el inventario.
                        </div>
                    @endif

                    @if(request('tipo'))
                        <div class="d-flex justify-content-center mt-3">
                            {{ $existencias->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
