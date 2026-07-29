@extends('layouts.stisla')

@section('title', 'Insumos del Producto')

@section('content')
@include('admin.partials.professional-styles')
@php $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false; @endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-4 px-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between">
                    <div>
                        <h2>Insumos del Producto</h2>
                        <p class="hero-subtitle">{{ $producto->nombre }}</p>
                    </div>
                    <div class="actions-bar d-flex flex-wrap gap-2 mt-4 mt-lg-0">
                        <a href="{{ route('admin.productos.edit', $producto->id) }}" class="btn btn-warning text-white">
                            <i class="fas fa-edit mr-1"></i> Editar Producto
                        </a>&nbsp;
                        <a href="{{ route('admin.productos.index') }}" class="btn btn-light border">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card section-card">
            <div class="card-body">
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="label">Clave</div>
                        <div class="value">{{ $producto->clave ?: '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Producto</div>
                        <div class="value">{{ $producto->nombre }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Tipo</div>
                        <div class="value">{{ $producto->tipoProducto->nombre ?? 'Sin tipo' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Total insumos</div>
                        <div class="value">{{ $producto->insumos->count() }}</div>
                    </div>
                    @if($veCostos)
                    <div class="meta-item">
                        <div class="label">Precio</div>
                        <div class="value money-value">
                            {{ $producto->precio !== null ? '$' . number_format((float) $producto->precio, 2) : '-' }}
                        </div>
                    </div>
                    @endif
                    <div class="meta-item">
                        <div class="label">Precio público</div>
                        <div class="value money-value">
                            {{ $producto->precio_publico !== null ? '$' . number_format((float) $producto->precio_publico, 2) : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card section-card">
            <div class="card-header">
                <h5>Listado de insumos asignados</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre del Insumo</th>
                                <th class="text-right">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($producto->insumos as $index => $insumo)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $insumo->nombre_completo }}</td>
                                    <td class="text-right">
                                        <span class="cantidad-badge">{{ number_format((float) ($insumo->pivot->cantidad ?? 0), 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No hay insumos asignados a este producto.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
