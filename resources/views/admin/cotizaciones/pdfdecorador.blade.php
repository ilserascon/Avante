<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización Decorador #{{ $cotizacion->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .header-table td { border: none; padding: 2px 6px; }
        .section-title { margin-top: 20px; font-weight: bold; }
        .no-border { border: none !important; }
        .firma { height: 40px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td rowspan="3" style="width: 35%;">
                <img src="{{ public_path('stisla/assets/img/Logo.jpg') }}" alt="logo" style="width: 150px;">
            </td>
            <td><strong>FOLIO:</strong> {{ str_pad($cotizacion->id, 4, '0', STR_PAD_LEFT) }}</td>
            <td><strong>FECHA:</strong> {{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>ASESOR DE VENTAS:</strong> {{ $cotizacion->asesor ?? 'Karla Pota' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>CLIENTE:</strong> {{ $cotizacion->cliente ? $cotizacion->cliente->nombre : '' }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>DIRECCIÓN:</strong> {{ $cotizacion->cliente ? $cotizacion->cliente->direccion : '' }}</td>
        </tr>
        <tr>
            <td><strong>CELULAR:</strong> {{ $cotizacion->cliente ? $cotizacion->cliente->telefono : '' }}</td>
            <td><strong>TELÉFONO:</strong> {{ $cotizacion->cliente ? $cotizacion->cliente->telefono : '' }}</td>
            <td></td>
        </tr>
    </table>

    <div class="section-title">DETALLE</div>
    <table>
        <thead>
            <tr>
                <th style="width:7%;">CANT.</th>
                <th style="width:38%;">DESCRIPCIÓN</th>
                <th style="width:15%;">MODELO</th>
                <th style="width:15%;">COLOR</th>
                <th style="width:12%;">PRECIO UNITARIO</th>
                <th style="width:13%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->insumos as $insumo)
                <tr>
                    <td class="text-right">{{ $insumo->pivot->cantidad }}</td>
                    <td>{{ $insumo->nombre }}</td>
                    <td>{{ $insumo->campo9 ?? '' }}</td>
                    <td>{{ $insumo->campo1 ?? '' }}</td>
                    <td class="text-right">&#36;{{ number_format($insumo->pivot->precio_unitario, 2) }}</td>
                    <td class="text-right">&#36;{{ number_format($insumo->pivot->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>SUBTOTAL</strong></td>
                <td class="text-right">&#36;{{ number_format($cotizacion->subtotal ?? $cotizacion->insumos->sum(fn($i) => $i->pivot->subtotal), 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>IVA</strong></td>
                <td class="text-right">&#36;{{ number_format($cotizacion->iva ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right">&#36;{{ number_format($cotizacion->total ?? ($cotizacion->subtotal ?? $cotizacion->insumos->sum(fn($i) => $i->pivot->subtotal)), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">TÉRMINOS Y CONDICIONES</div>
    <ol style="margin-bottom: 10px;">
        <li>Se requiere el % de anticipo y el resto al instalar.</li>
        <li>Instalación incluida en nuestros trabajos.</li>
        <li>Tiempo de entrega es de ___ días hábiles posteriores al anticipo.</li>
        <li>En compras diferidas a meses sin intereses se realiza en una sola exhibición.</li>
        <li>Si por algún motivo la instalación es aplazada por motivos ajenos a nosotros, el cliente deberá liquidar el resto de la cotización.</li>
    </ol>

    <div class="section-title">RECIBO DE ANTICIPO</div>
    <table>
        <tr>
            <td class="no-border"><strong>RECIBÍ LA CANTIDAD DE $</strong></td>
            <td class="no-border" style="border-bottom: 1px solid #ccc; min-width: 80px;">{{ $cotizacion->anticipo ?? '' }}</td>
            <td class="no-border"><strong>POR CONCEPTO DE</strong></td>
            <td class="no-border">[ ] ANTICIPO [ ] PAGO TOTAL</td>
        </tr>
        <tr>
            <td class="no-border"><strong>RESTANDO LA CANTIDAD DE $</strong></td>
            <td class="no-border" style="border-bottom: 1px solid #ccc;">{{ $cotizacion->restante ?? '' }}</td>
            <td class="no-border"><strong>FIRMA</strong></td>
            <td class="no-border firma"></td>
        </tr>
        <tr>
            <td class="no-border"><strong>NOMBRE DEL CLIENTE</strong></td>
            <td class="no-border" style="border-bottom: 1px solid #ccc;">{{ $cotizacion->cliente ? $cotizacion->cliente->nombre : '' }}</td>
            <td class="no-border"><strong>FIRMA DEL CLIENTE</strong></td>
            <td class="no-border firma"></td>
        </tr>
    </table>

    <p style="margin-top: 10px; font-size: 11px;">
        Si usted tiene alguna duda sobre esta cotización, por favor, póngase en contacto con nosotros.
    </p>
</body>
</html>