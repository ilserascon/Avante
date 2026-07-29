@extends('layouts.stisla')

@section('title', 'Detalle de Insumo')

@section('content')
@include('admin.partials.professional-styles')
@php $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false; @endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>{{ $insumo->nombre }}</h3>
                        <p class="hero-subtitle">Detalle del insumo</p>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.insumos.edit', $insumo->id) }}" class="btn btn-warning text-white px-4">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>&nbsp;
                        <a href="{{ route('admin.insumos.index', ['tipo_insumo' => $insumo->id_tipo_insumo]) }}" class="btn btn-light border px-4">
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
                        <div class="value">{{ $insumo->clave ?: '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Nombre</div>
                        <div class="value">{{ $insumo->nombre }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Color</div>
                        <div class="value">{{ $insumo->color ?: '-' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Proveedor</div>
                        <div class="value">{{ $insumo->proveedor->nombre ?? 'N/A' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Tipo de insumo</div>
                        <div class="value">{{ $insumo->tipoInsumo->nombre ?? 'N/A' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="label">Estado</div>
                        <div class="value">
                            @if($insumo->borrado == 0)
                                <span class="status-chip status-active">Activo</span>
                            @else
                                <span class="status-chip status-inactive">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header">
                <h5>Precios</h5>
            </div>
            <div class="card-body">
                <div class="meta-grid">
                    @if($veCostos)
                    <div class="meta-item">
                        <div class="label">Costo</div>
                        <div class="value money-value">${{ number_format((float) $insumo->costo, 2) }}</div>
                    </div>
                    @endif
                    <div class="meta-item">
                        <div class="label">Precio público</div>
                        <div class="value money-value">${{ number_format((float) $insumo->precio_publico, 2) }}</div>
                    </div>
                    @if($veCostos)
                    <div class="meta-item">
                        <div class="label">Utilidad</div>
                        <div class="value">{{ number_format((float) $insumo->utilidad, 2) }}</div>
                    </div>
                    @endif
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
                                <div class="value">{{ $insumo->$campo ?: '-' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
