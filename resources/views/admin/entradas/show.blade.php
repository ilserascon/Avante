@extends('layouts.stisla')

@section('title', 'Detalles de la Entrada')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Detalles de la Entrada</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Detalles de la Entrada #{{ $entrada->id }}</h4>
            </div>
            <div class="card-body">
                <p><strong>Almacén:</strong> {{ $entrada->id_almacen }}</p>
                <p><strong>Usuario:</strong> {{ $entrada->id_usuario }}</p>
                <p><strong>Fecha:</strong> {{ $entrada->created_at }}</p>

                <h5>Productos/Insumos:</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto/Insumo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entrada->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre ?? $detalle->insumo->nombre }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <a href="{{ route('admin.entradas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection