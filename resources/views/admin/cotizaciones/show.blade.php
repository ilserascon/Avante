@extends('layouts.stisla')

@section('title', 'Detalle de Cotización')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Detalle de la Cotización</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Cotización #{{ $cotizacion->id }}</h4>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $cotizacion->cliente->nombre }}</p>
                <p><strong>Producto:</strong> {{ $cotizacion->producto->nombre }}</p>
                <p><strong>Insumo:</strong> {{ $cotizacion->insumo->nombre }}</p>
                <p><strong>Total:</strong> ${{ number_format($cotizacion->total, 2) }}</p>
                <p><strong>Estatus:</strong> {{ ucfirst($cotizacion->estatus) }}</p>
                <p><strong>Detalles:</strong></p>
                <pre>{{ json_encode($cotizacion->detalles, JSON_PRETTY_PRINT) }}</pre>
            </div>
            <div class="card-footer">
                <a href="https://wa.me/?text={{ urlencode(route('admin.cotizaciones.show', $cotizacion->id)) }}" target="_blank" class="btn btn-success">Enviar por WhatsApp</a>
            </div>
        </div>
    </div>
</div>
@endsection
