@extends('layouts.stisla')

@section('title', 'Tipos de Insumo')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Tipos de Insumo</h3>
                        <p class="hero-subtitle">Configure los tipos y campos personalizados de insumos.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.tipo-insumos.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo Tipo
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
                                <th>Nombre</th>
                                @for($i = 1; $i <= 15; $i++)
                                    <th>Campo {{ $i }}</th>
                                @endfor
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipoInsumos as $tipoInsumo)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.tipo-insumos.edit', $tipoInsumo->id) }}" class="record-link">
                                            {{ $tipoInsumo->nombre }}
                                        </a>
                                    </td>
                                    @for($i = 1; $i <= 15; $i++)
                                        <td>{{ $tipoInsumo->{'campo'.$i} ?: '-' }}</td>
                                    @endfor
                                    <td>
                                        <div class="actions-wrap">
                                            <a href="{{ route('admin.tipo-insumos.edit', $tipoInsumo->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.tipo-insumos.plantilla', $tipoInsumo->id) }}" class="action-btn btn-enable" title="Descargar plantilla Excel para importar">
                                                <i class="fas fa-file-excel"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="17" class="text-center text-muted py-4">No hay tipos de insumo registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $tipoInsumos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
