@extends('layouts.stisla')

@section('title', 'Almacenes')

@section('content')
<style>
    .almacenes-page .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .almacenes-page .hero-card h1 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #26344d;
    }

    .almacenes-page .table-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
    }

    .almacenes-page .table thead th {
        background: #f5f8ff;
        color: #4a5f83;
        font-weight: 700;
        border-bottom: 0;
        white-space: nowrap;
    }

    .almacenes-page .table tbody td {
        vertical-align: middle;
    }

    .almacenes-page .table tbody tr {
        transition: all .25s ease;
    }

    .almacenes-page .table tbody tr:hover {
        background: #f8fbff;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
    }

    .almacenes-page .table tbody tr:hover td {
        color: #243b63;
        background: #f8fbff;
    }

    .almacenes-page .almacen-nombre {
        font-weight: 700;
        color: #1f3a69;
    }

    .almacenes-page .actions-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .almacenes-page .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.75rem;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        border: 1px solid transparent;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }

    .almacenes-page .action-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .almacenes-page .btn-inventario {
        background: #e7f1ff;
        color: #1d4ed8;
        border-color: #cfe2ff;
    }

    .almacenes-page .btn-inventario:hover {
        background: #dbeafe;
        color: #1e40af;
    }

    .almacenes-page .btn-edit {
        background: #fff5df;
        color: #b45309;
        border-color: #ffe3ae;
    }

    .almacenes-page .btn-edit:hover {
        background: #ffedd5;
        color: #92400e;
    }

    @media (max-width: 767.98px) {
        .almacenes-page .hero-actions {
            margin-top: 0.75rem;
            width: 100%;
        }

        .almacenes-page .hero-actions .btn {
            width: 100%;
        }

        .almacenes-page .actions-wrap {
            justify-content: flex-start;
        }
    }
</style>

<div class="section">
    <div class="almacenes-page">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Almacenes</h3>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.almacenes.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo almacén
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card table-card">
                <div class="card-body table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Ubicación</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($almacenes as $almacen)
                                <tr>
                                    <td class="almacen-nombre">{{ $almacen->nombre }}</td>
                                    <td>{{ $almacen->ubicacion ?: '—' }}</td>
                                    <td>
                                        <div class="actions-wrap">
                                            <a href="{{ route('admin.almacenes.existencia', $almacen->id) }}" class="action-btn btn-inventario" title="Inventario">
                                                <i class="fas fa-boxes"></i> Inventario
                                            </a>
                                            <a href="{{ route('admin.almacenes.edit', $almacen->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No hay almacenes registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $almacenes->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
