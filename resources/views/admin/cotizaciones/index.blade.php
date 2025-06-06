@extends('layouts.stisla')

@section('title', 'Cotizaciones')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Cotizaciones</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.cotizaciones.create') }}" class="btn btn-primary">Nueva Cotización</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.cotizaciones.index') }}" class="mb-4">
        <div class="form-row">
            <div class="col">
                <select name="estatus" class="form-control">
                    <option value="">Todos los estatus</option>
                    <option value="solicitada" {{ request('estatus') == 'solicitada' ? 'selected' : '' }}>Solicitada</option>
                    <option value="aceptada" {{ request('estatus') == 'aceptada' ? 'selected' : '' }}>Aceptada</option>
                    <option value="rechazada" {{ request('estatus') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
            <div class="col">
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control" placeholder="Desde">
            </div>
            <div class="col">
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control" placeholder="Hasta">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Buscar</button>
                <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </div>
    </form>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Listado de Cotizaciones</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Forro</th>
                            <th>Total Lienzos</th>
                            <th>M2 Tela</th>
                            <th>M2 Tergal</th>
                            <th>M2 Forro</th>
                            <th>Costo Cortina</th>
                            @if(auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador')
                                <th>Utilidad</th>
                                <th>Costo Decorador</th>
                            @endif
                            <th>Precio Público</th>
                            <th>Fecha</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cotizaciones as $cotizacion)
                            <tr>
                                <td>{{ $cotizacion->id }}</td>
                                <td>{{ $cotizacion->cliente ? $cotizacion->cliente->nombre : 'N/A' }}</td>
                                <td>
                                    @php
                                        $tipos = [];
                                        if($cotizacion->lleva_cortina) $tipos[] = 'Cortina';
                                        if($cotizacion->lleva_tergal) $tipos[] = 'Tergal';
                                    @endphp
                                    {{ implode(', ', $tipos) }}
                                </td>
                                <td>{{ $cotizacion->lleva_forro ? 'Sí' : 'No' }}</td>
                                <td>{{ $cotizacion->total_lienzos ?? '-' }}</td>
                                <td>{{ $cotizacion->total_m2_tela ?? '-' }}</td>
                                <td>{{ $cotizacion->total_m2_tergal ?? '-' }}</td>
                                <td>{{ $cotizacion->total_m2_forro ?? '-' }}</td>
                                <td>${{ number_format($cotizacion->costo_cortina, 2) }}</td>
                                @if(auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador')
                                    <td>${{ number_format($cotizacion->utilidad, 2) }}</td>
                                    <td>${{ number_format($cotizacion->costo_decorador, 2) }}</td>
                                @endif
                                <td>${{ number_format($cotizacion->precio_publico, 2) }}</td>
                                <td>{{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</td>
                                <td>{{ ucfirst($cotizacion->estatus) }}</td>
                                <td class="d-inline-flex align-items-center">
                                    <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-info btn-sm" style="margin-right: 0.5rem;" title="Ver detalles">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('admin.cotizaciones.pdf', $cotizacion->id) }}" class="btn btn-primary btn-sm" style="margin-right: 0.5rem;" target="_blank">
                                        <i class="fas fa-file-pdf"></i> PDF Cliente
                                    </a>
                                    @if(auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador')
                                        <a href="{{ route('admin.cotizaciones.pdf-decorador', $cotizacion->id) }}" class="btn btn-primary btn-sm" style="margin-right: 0.5rem;" target="_blank">
                                            <i class="fas fa-file-pdf"></i> PDF Decorador
                                        </a>
                                    @endif
                                    @if($cotizacion->estatus === 'solicitada')
                                        <form action="{{ route('admin.cotizaciones.cambiar-estatus', $cotizacion->id) }}" method="POST" class="d-inline mb-0" style="margin-right: 0.5rem;">
                                            @csrf
                                            <input type="hidden" name="estatus" value="aceptada">
                                            <button type="submit" class="btn btn-success btn-sm" title="Aceptar">
                                                <i class="fas fa-check"></i> Aceptar
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.cotizaciones.cambiar-estatus', $cotizacion->id) }}" method="POST" class="d-inline mb-0" style="margin-right: 0.5rem;">
                                            @csrf
                                            <input type="hidden" name="estatus" value="rechazada">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Rechazar">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="btn btn-warning btn-sm" style="margin-right: 0.5rem;" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $cotizaciones->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cortina = document.getElementById('chkCortina');
        const tergal = document.getElementById('chkTergal');
        const forro = document.getElementById('chkForro');

        const sectionCortina = document.getElementById('sectionCortina');
        const sectionTergal = document.getElementById('sectionTergal');
        const sectionForro = document.getElementById('sectionForro');

        function toggleSections() {
            sectionCortina.classList.toggle('d-none', !cortina.checked);
            sectionTergal.classList.toggle('d-none', !tergal.checked);
            sectionForro.classList.toggle('d-none', !forro.checked);
        }

        cortina.addEventListener('change', toggleSections);
        tergal.addEventListener('change', toggleSections);
        forro.addEventListener('change', toggleSections);
    });
</script>
@endpush
