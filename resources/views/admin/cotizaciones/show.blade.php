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
                <p><strong>Cliente:</strong> {{ $cotizacion->cliente ? $cotizacion->cliente->nombre : 'N/A' }}</p>
                <p><strong>Fecha:</strong> {{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</p>
                <p><strong>Estatus:</strong> {{ ucfirst($cotizacion->estatus) }}</p>
                <p>
                    <strong>Tipo:</strong>
                    @php
                        $tipos = [];
                        if($cotizacion->lleva_cortina) $tipos[] = 'Cortina';
                        if($cotizacion->lleva_tergal) $tipos[] = 'Tergal';
                    @endphp
                    {{ implode(', ', $tipos) ?: '-' }}
                </p>
                <p><strong>Lleva Forro:</strong> {{ $cotizacion->lleva_forro ? 'Sí' : 'No' }}</p>
                <hr>
                <p><strong>Total Lienzos:</strong> {{ $cotizacion->total_lienzos ?? '-' }}</p>
                <p><strong>M2 Tela:</strong> {{ $cotizacion->total_m2_tela ?? '-' }}</p>
                <p><strong>M2 Tergal:</strong> {{ $cotizacion->total_m2_tergal ?? '-' }}</p>
                <p><strong>M2 Forro:</strong> {{ $cotizacion->total_m2_forro ?? '-' }}</p>
                <p><strong>Costo Cortina:</strong> ${{ number_format($cotizacion->costo_cortina, 2) }}</p>
                @if(auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador')
                    <hr>
                    <p><strong>Utilidad:</strong> ${{ number_format($cotizacion->utilidad, 2) }}</p>
                    <p><strong>Costo Decorador:</strong> ${{ number_format($cotizacion->costo_decorador, 2) }}</p>
                @endif
                <p><strong>Precio Público:</strong> ${{ number_format($cotizacion->precio_publico, 2) }}</p>
                @if(isset($cotizacion->detalles))
                    <hr>
                    <p><strong>Detalles:</strong></p>
                    <pre>{{ json_encode($cotizacion->detalles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
                <hr>
                <h5>Insumos utilizados</h5>
                @if($cotizacion->insumos && $cotizacion->insumos->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Precio unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->insumos as $insumo)
                                    <tr>
                                        <td>{{ $insumo->nombre }}</td>
                                        <td>{{ $insumo->pivot->cantidad }}</td>
                                        <td>${{ number_format($insumo->pivot->precio_unitario, 2) }}</td>
                                        <td>${{ number_format($insumo->pivot->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No hay insumos registrados para esta cotización.</p>
                @endif
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12 col-md-6 mb-2 mb-md-0 d-flex">
                        <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary btn-sm flex-fill mr-2" style="max-width:200px;">
                            Volver al listado
                        </a>
                        @php
                            $cliente = $cotizacion->cliente;
                            $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono ?? '');
                            $urlPdf = asset('storage/pdfs/cotizacion_' . $cotizacion->id . '.pdf');
                            $mensaje = urlencode("Hola {$cliente->nombre}, aquí puedes descargar tu cotización en PDF:\n{$urlPdf}");
                        @endphp

                        @if($telefono)
                            <a href="https://wa.me/52{{ $telefono }}?text={{ $mensaje }}" target="_blank"
                                class="btn btn-success btn-sm flex-fill"
                                style="background-color: #25D366; border-color: #25D366; max-width:200px;">
                                <i class="fab fa-whatsapp"></i> Enviar por WhatsApp
                            </a>
                        @else
                            <button class="btn btn-success btn-sm flex-fill" style="max-width:200px;" disabled>WhatsApp no disponible</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
