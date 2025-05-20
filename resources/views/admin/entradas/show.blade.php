@extends('layouts.stisla')
@section('title', 'Detalle de Entrada')
@section('content')
<div class="section">
    <div class="section-header">
        <h1>Detalle de Entrada #{{ $entrada->id }}</h1>
        <a href="{{ route('admin.entradas.index') }}" class="btn btn-secondary ml-auto">Volver</a>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-header"><h4>Detalles</h4></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Insumo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entrada->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? '-' }}</td>
                            <td>
                                {{ $detalle->insumo->nombre_completo ?? '-' }}
                            </td>
                            <td>{{ $detalle->cantidad }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection