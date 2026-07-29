@extends('layouts.stisla')

@section('title', 'Productos')

@section('content')
@include('admin.partials.professional-styles')
@php $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false; @endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Productos</h3>
                        <p class="hero-subtitle">Gestione el catálogo de productos.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.productos.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo Producto
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.productos.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="field-label">Nombre o clave</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre o clave" value="{{ request('nombre') }}">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="field-label">Tipo de producto</label>
                            <select name="id_tipo_producto" class="form-control">
                                <option value="">Todos los tipos</option>
                                @foreach ($tiposProducto as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('id_tipo_producto') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.productos.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
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
                                <th>Clave</th>
                                <th>Nombre</th>
                                <th>Color</th>
                                <th>Tipo</th>
                                @if($veCostos)
                                <th>Precio</th>
                                @endif
                                <th>Precio público</th>
                                @foreach($camposDinamicos as $campo => $etiqueta)
                                    <th>{{ $etiqueta }}</th>
                                @endforeach
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $columnasTabla = 6 + ($veCostos ? 1 : 0) + count($camposDinamicos) + 1;
                            @endphp
                            @forelse ($productos as $producto)
                                @php
                                    $esCortinero = $producto->id_tipo_producto == 1 || strtolower($producto->tipoProducto->nombre ?? '') === 'cortinero';
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.productos.show', $producto->id) }}" class="record-link">
                                            {{ $producto->clave ?: '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.productos.show', $producto->id) }}" class="record-link">
                                            {{ $producto->nombre }}
                                        </a>
                                    </td>
                                    <td>{{ $producto->color ?: '-' }}</td>
                                    <td>{{ $producto->tipoProducto->nombre ?? 'Sin tipo' }}</td>
                                    @if($veCostos)
                                    <td class="money-value">
                                        {{ $producto->precio !== null ? '$' . number_format((float) $producto->precio, 2) : '-' }}
                                    </td>
                                    @endif
                                    <td class="money-value">
                                        {{ $producto->precio_publico !== null ? '$' . number_format((float) $producto->precio_publico, 2) : '-' }}
                                    </td>
                                    @foreach($camposDinamicos as $campo => $etiqueta)
                                        <td>{{ $producto->$campo ?: '-' }}</td>
                                    @endforeach
                                    <td>
                                        <div class="actions-wrap">
                                            <a href="{{ route('admin.productos.edit', $producto->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $columnasTabla }}" class="text-center text-muted py-4">No se encontraron productos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $productos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
