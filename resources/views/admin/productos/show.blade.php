@extends('layouts.stisla')

@section('title', 'Detalle de Producto')

@section('content')
@include('admin.partials.professional-styles')

@php
    $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false;
    $esCortinero = $producto->id_tipo_producto == 1 || strtolower($producto->tipoProducto->nombre ?? '') === 'cortinero';
@endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>{{ $producto->nombre }}</h3>
                        <p class="hero-subtitle">Detalle del producto</p>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.productos.edit', $producto->id) }}" class="btn btn-warning text-white px-4">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>&nbsp;
                        <a href="{{ route('admin.productos.index') }}" class="btn btn-light border px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header">
                <h5>Información general</h5>
            </div>
            <div class="card-body">
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="label">Clave</div>
                        <div class="value">{{ $producto->clave ?: '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Nombre</div>
                        <div class="value">{{ $producto->nombre }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Color</div>
                        <div class="value">{{ $producto->color ?: '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Tipo</div>
                        <div class="value">{{ $producto->tipoProducto->nombre ?? 'Sin tipo' }}</div>
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
                <div class="mt-3">
                    <div class="label field-label">Descripción</div>
                    <div class="value">{{ $producto->descripcion ?: '-' }}</div>
                </div>
            </div>
        </div>

        @if(!empty($camposDinamicos))
            <div class="card form-card mb-4">
                <div class="card-header">
                    <h5>Campos adicionales</h5>
                </div>
                <div class="card-body">
                    <div class="meta-grid">
                        @foreach($camposDinamicos as $campo => $etiqueta)
                            <div class="meta-item">
                                <div class="label">{{ $etiqueta }}</div>
                                <div class="value">{{ $producto->$campo ?: '-' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if ($esCortinero && $producto->insumos->isNotEmpty())
            <div class="card form-card">
                <div class="card-header">
                    <h5>Insumos asignados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Clave</th>
                                    <th>Nombre del insumo</th>
                                    <th class="text-right">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($producto->insumos as $index => $insumo)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $insumo->clave ?: '-' }}</td>
                                        <td>{{ $insumo->nombre_completo }}</td>
                                        <td class="text-right">
                                            <span class="cantidad-badge">{{ number_format((float) ($insumo->pivot->cantidad ?? 0), 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
