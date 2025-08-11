<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
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
            <td colspan="2"><strong>ASESOR DE VENTAS:</strong></td>
        </tr>
        <tr>
            <td colspan="2"><strong>CLIENTE:</strong> {{ $cotizacion->cliente->nombre ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>DIRECCIÓN:</strong> {{ $cotizacion->cliente->direccion ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>CELULAR:</strong> {{ $cotizacion->cliente->celular ?? '' }}</td>
            <td><strong>TELÉFONO:</strong> {{ $cotizacion->cliente->telefono ?? '' }}</td>
            <td></td>
        </tr>
    </table>

    <div class="section-title">Detalle de Cotización</div>
    <table>
        <tr>
            <th style="width: 40%;">Tipo de Cortina</th>
            <th style="width: 60%;">Precio Público</th>
        </tr>
        <tr>
            <td>{{ $cotizacion->detalleCotizacion->tipo_cortina ?? '-' }}</td>
            <td>${{ number_format($cotizacion->precio_publico ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">Insumos utilizados</div>
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
            @php 
                $subtotal = 0; 
                $insumosUsados = collect();

                if($cotizacion->detalleCotizacion) {
                    foreach($cotizacion->detalleCotizacion->toArray() as $campo => $valor) {
                        if(str_ends_with($campo, '_id') && $valor) {
                            $cantidadCampo = str_replace('_id', '_cantidad', $campo);
                            $cantidad = $cotizacion->detalleCotizacion->$cantidadCampo ?? 0;
                            $insumo = \App\Models\Insumo::find($valor);
                            if($insumo) {
                                $tipoNombre = $insumo->tipoInsumo->nombre ?? '';
                                if(in_array($tipoNombre, ['Telas', 'Forro', 'Tergal']) && (!$cantidad || $cantidad <= 0)) {
                                    $cantidad = 1;
                                }
                                $insumosUsados->push([
                                    'insumo' => $insumo,
                                    'cantidad' => $cantidad
                                ]);
                            }
                        }
                    }
                }

                foreach($cotizacion->insumos as $insumoRel) {
                    $cantidad = $insumoRel->pivot->cantidad;
                    $tipoNombre = $insumoRel->tipoInsumo->nombre ?? '';
                    if(in_array($tipoNombre, ['Telas', 'Forro', 'Tergal']) && (!$cantidad || $cantidad <= 0)) {
                        $cantidad = 1;
                    }
                    $insumosUsados->push([
                        'insumo' => $insumoRel,
                        'cantidad' => $cantidad
                    ]);
                }


                $insumosAgrupados = [];
                foreach ($insumosUsados as $item) {
                    $id = $item['insumo']->id;
                    if (isset($insumosAgrupados[$id])) {
                        $insumosAgrupados[$id]['cantidad'] += $item['cantidad'];
                    } else {
                        $insumosAgrupados[$id] = [
                            'insumo' => $item['insumo'],
                            'cantidad' => $item['cantidad'],
                        ];
                    }
                }
            @endphp

            @foreach($insumosAgrupados as $item)
                @php
                    $precio = $item['insumo']->precio_publico ?? 0;
                    $sub = $precio * $item['cantidad'];
                    $subtotal += $sub;
                @endphp
                <tr>
                    <td>{{ $item['insumo']->nombre }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>${{ number_format($precio, 2) }}</td>
                    <td>${{ number_format($sub, 2) }}</td>
                </tr>
            @endforeach


            @for($i = 0; $i < 2; $i++)
                <tr>
                    <td style="height:22px;"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            @php
                $iva = $subtotal * 0.16;
                $total = $subtotal + $iva;
            @endphp
            <tr>
                <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>IVA (16%)</strong></td>
                <td>${{ number_format($iva, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Total</strong></td>
                <td>${{ number_format($total, 2) }}</td>
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
            <td class="no-border" style="border-bottom: 1px solid #ccc;">{{ $cotizacion->cliente->nombre ?? '' }}</td>
            <td class="no-border"><strong>FIRMA DEL CLIENTE</strong></td>
            <td class="no-border firma"></td>
        </tr>
    </table>

    <p style="margin-top: 10px; font-size: 11px;">
        Si usted tiene alguna duda sobre esta cotización, por favor, póngase en contacto con nosotros.
    </p>
</body>
</html>
