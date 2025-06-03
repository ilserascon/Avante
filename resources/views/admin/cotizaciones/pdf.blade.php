<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f5f5f5; }
        h2, h4 { margin-bottom: 0; }
        .logo-container { text-align: left; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="{{ public_path('stisla/assets/img/Logo.jpg') }}" alt="logo" style="width: 200px;">
    </div>
    <h2>Cotización #{{ $cotizacion->id }}</h2>
    <p><strong>Cliente:</strong> {{ $cotizacion->cliente ? $cotizacion->cliente->nombre : 'N/A' }}</p>
    <p><strong>Fecha:</strong> {{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</p>
    <p><strong>Estatus:</strong> {{ ucfirst($cotizacion->estatus) }}</p>

    <table>
        <tr>
            <th>Tipo</th>
            <th>Lleva Forro</th>
            <th>Total Lienzos</th>
            <th>Total m² Tela</th>
            <th>Total m² Tergal</th>
            <th>Total m² Forro</th>
            <th>Precio Público</th>
        </tr>
        <tr>
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
            <td>${{ number_format($cotizacion->precio_publico, 2) }}</td>
        </tr>
    </table>

    <h4 style="margin-top:30px;">Insumos utilizados</h4>
    @if($cotizacion->insumos && $cotizacion->insumos->count())
        <table>
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
    @else
        <p>No hay insumos registrados para esta cotización.</p>
    @endif
</body>
</html>