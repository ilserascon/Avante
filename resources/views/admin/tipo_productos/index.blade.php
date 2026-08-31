@extends('layouts.stisla')

@section('title', 'Tipos de Producto')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Tipos de Producto</h3>
                        <p class="hero-subtitle">Configure las categorías del catálogo de productos.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.tipo-productos.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo Tipo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.tipo-productos.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-8 mb-2 mb-md-0">
                            <label class="field-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre" value="{{ request('nombre') }}">
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.tipo-productos.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="section-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card table-card">
                <div class="card-body table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tipoProductos as $tipoProducto)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.tipo-productos.edit', $tipoProducto->id) }}" class="record-link">
                                            {{ $tipoProducto->nombre }}
                                        </a>
                                    </td>
                                    <td>{{ $tipoProducto->descripcion ?: '-' }}</td>
                                    <td>
                                        <div class="actions-wrap">
                                            <a href="{{ route('admin.tipo-productos.edit', $tipoProducto->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.tipo-productos.plantilla', $tipoProducto->id) }}" class="action-btn btn-enable" title="Descargar plantilla Excel para importar">
                                                <i class="fas fa-file-excel"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No se encontraron tipos de producto.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $tipoProductos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
