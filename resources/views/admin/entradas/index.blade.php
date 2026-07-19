@extends('layouts.stisla')

@section('title', 'Entradas')

@section('content')
<style>
    .entradas-page .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .entradas-page .hero-card h3 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #26344d;
    }

    .entradas-page .table-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
    }

    .entradas-page .table thead th {
        background: #f5f8ff;
        color: #4a5f83;
        font-weight: 700;
        border-bottom: 0;
        white-space: nowrap;
    }

    .entradas-page .table tbody td {
        vertical-align: middle;
    }

    .entradas-page .table tbody tr {
        transition: all .25s ease;
    }

    .entradas-page .table tbody tr:hover {
        background: #f8fbff;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
    }

    .entradas-page .table tbody tr:hover td {
        color: #243b63;
        background: #f8fbff;
    }

    .entradas-page .entrada-link {
        font-weight: 700;
        color: #4e73df;
        text-decoration: none;
        transition: .2s;
    }

    .entradas-page .entrada-link:hover {
        color: #1a8683;
        text-decoration: none;
    }

    .entradas-page .table tbody tr:hover .entrada-link {
        color: #1a8683;
    }

    .entradas-page .actions-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: flex-end;
    }

    .entradas-page .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        transition: all 0.15s ease-in-out;
    }

    .entradas-page .action-btn i {
        font-size: 0.85rem;
    }

    .entradas-page .action-btn:hover {
        transform: translateY(-1px);
    }

    .entradas-page .btn-see {
        background: #e7f1ff;
        color: #1d4ed8;
        border-color: #cfe2ff;
    }

    .entradas-page .btn-edit {
        background: #fff5df;
        color: #b45309;
        border-color: #ffe3ae;
    }

    @media (max-width: 767.98px) {
        .entradas-page .hero-actions {
            margin-top: 0.75rem;
            width: 100%;
        }

        .entradas-page .hero-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="section">
    <div class="entradas-page">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Entradas</h3>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.entradas.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nueva Entrada
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
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Almacén</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($entradas as $entrada)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.entradas.show', $entrada->id) }}" class="entrada-link">
                                            {{ str_pad($entrada->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td>{{ $entrada->almacen->nombre ?? 'N/A' }}</td>
                                    <td>{{ $entrada->usuario->name ?? 'N/A' }}</td>
                                    <td>{{ $entrada->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="actions-wrap">
                                            <a href="{{ route('admin.entradas.show', $entrada->id) }}" class="action-btn btn-see" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.entradas.edit', $entrada->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay entradas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $entradas->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
