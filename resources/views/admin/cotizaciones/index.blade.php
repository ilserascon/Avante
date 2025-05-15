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
                            <th>Utilidad</th>
                            <th>Costo Decorador</th>
                            <th>Precio Público</th>
                            <th>Fecha</th>
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
                                <td>${{ number_format($cotizacion->utilidad, 2) }}</td>
                                <td>${{ number_format($cotizacion->costo_decorador, 2) }}</td>
                                <td>${{ number_format($cotizacion->precio_publico, 2) }}</td>
                                <td>{{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $cotizaciones->links() }}
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
