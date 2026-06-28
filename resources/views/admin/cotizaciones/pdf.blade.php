<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; margin: 0; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 5px 0; }
        th, td { border: 1px solid #333; padding: 5px; }
        th { background: #17a2b8; color: white; text-align: center; font-weight: bold; }
        .header-table td { border: none; padding: 2px 6px; }
        .header-table { margin-bottom: 10px; }
        .section-title { margin-top: 15px; margin-bottom: 8px; font-weight: bold; background: #17a2b8; color: white; padding: 5px; }
        .no-border { border: none !important; }
        .firma { height: 40px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .empresa-info { text-align: center; font-size: 10px; margin-bottom: 10px; }
        .empresa-nombre { font-size: 16px; font-weight: bold; }
        .empresa-redes { font-size: 9px; margin-top: 3px; }
        .logo-area { width: 25%; text-align: center; vertical-align: top; }
        .info-area { width: 75%; }
        .header-info { border: none; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 30%; border:none; vertical-align: top;">
                <img src="{{ public_path('stisla/assets/img/Logo.jpg') }}" alt="logo" style="width: 180px;">
            </td>
            <td style="width: 70%; border:none; vertical-align: top; padding-left: 10px;">
                <div class="empresa-info" style="text-align: left; margin-bottom: 0;">
                    <div class="empresa-nombre" style="font-size: 14px;">AVANTE DECORACIONES</div>
                    <div style="font-size: 10px;">BULEVAR MORELOS 471, SABINOS RESIDENCIAL</div>
                    <div style="font-size: 10px;">HERMOSILLO, SONORA CP 83148</div>
                    <div style="font-size: 10px; margin-top: 2px;">TELÉFONO | CELULAR</div>
                    <div class="empresa-redes" style="font-size: 10px; margin-top: 3px;">FACEBOOK: Avante Decoraciones | INSTAGRAM: @avantedecoraciones</div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; border-bottom: 1px solid #ccc;">
                <strong>COTIZACIÓN Nº:</strong> {{ str_pad($cotizacion->id, 4, '0', STR_PAD_LEFT) }}
            </td>
            <td style="width: 50%; border-bottom: 1px solid #ccc;">
                <strong>FECHA:</strong> {{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid #ccc;">
                <strong>ASESOR DE VENTA:</strong> IRACEMA SANCHEZ
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid #ccc;">
                <strong>CLIENTE:</strong> {{ $cotizacion->cliente->nombre ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom: 1px solid #ccc;">
                <strong>DIRECCIÓN:</strong> {{ $cotizacion->cliente->direccion ?? '-' }}
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #ccc;">
                <strong>CELULAR:</strong> {{ $cotizacion->cliente->celular ?? '-' }}
            </td>
            <td style="border-bottom: 1px solid #ccc;">
                <strong>TELÉFONO:</strong> {{ $cotizacion->cliente->telefono ?? '-' }}
            </td>
        </tr>
    </table>

    <div class="section-title">DETALLE DE COTIZACIÓN</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">U</th>
                <th style="width: 20%;">DESCRIPCIÓN</th>
                <th style="width: 10%;">CANTIDAD</th>
                <th style="width: 15%;">ÁREA</th>
                <th style="width: 25%;">TELAS</th>
                <th style="width: 15%;">TIPO DE CORTINA</th>
                <th style="width: 10%;">PRECIO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowNum = 1;
                $subtotal = 0;
                $detalles = [];
                $idsExcluidos = [];

                if($cotizacion->detalleCotizacion) {
                    $detalleC = $cotizacion->detalleCotizacion;

                    $insumoTela = $detalleC->tela_id ? \App\Models\Insumo::find($detalleC->tela_id) : null;
                    $insumoTergal = $detalleC->tergal_id ? \App\Models\Insumo::find($detalleC->tergal_id) : null;
                    $insumoForro = $detalleC->forro_id ? \App\Models\Insumo::find($detalleC->forro_id) : null;

                    $idsExcluidos = array_filter([$detalleC->tela_id, $detalleC->tergal_id, $detalleC->forro_id]);

                    if($detalleC->total_tela_final && $detalleC->total_tela_final > 0) {
                        $cortineroSum = ($detalleC->cortinero_cantidad ?? 0) * ($detalleC->cortinero_precio ?? 0);
                        $precioTela = ($detalleC->total_tela_final + ($detalleC->total_mano_obra_1 ?? 0) + $cortineroSum + ($detalleC->total_final_forro ?? 0)) * 2;
                        $descripcionCortina = 'CORTINA' . (($detalleC->total_final_forro ?? 0) > 0 ? ' CON FORRO' : '');
                        $detalles[] = [
                            'descripcion' => $descripcionCortina,
                            'cantidad' => 1,
                            'area' => $cotizacion->area ?? '',
                            'tela' => $insumoTela?->nombre ?? $detalleC->descripcion_tela ?? '',
                            'tipo_cortina' => $detalleC->tipo_cortina ?? '',
                            'precio' => $precioTela
                        ];
                    }
                    if($detalleC->total_tergal_final && $detalleC->total_tergal_final > 0) {
                        $cortineroTergalSum = ($detalleC->cortinero_tergal_cantidad ?? 0) * ($detalleC->cortinero_tergal_precio ?? 0);
                        $precioTergal = ($detalleC->total_tergal_final + ($detalleC->total_mano_obra_2 ?? 0) + $cortineroTergalSum) * 2;
                        $detalles[] = [
                            'descripcion' => 'TERGAL',
                            'cantidad' => 1,
                            'area' => '',
                            'tela' => $insumoTergal?->nombre ?? $detalleC->descripcion_tergal ?? '',
                            'tipo_cortina' => '',
                            'precio' => $precioTergal
                        ];
                    }
                }

                foreach($cotizacion->insumos as $insumoRel) {
                    if(in_array($insumoRel->id, $idsExcluidos)) {
                        continue;
                    }

                    $cantidad = $insumoRel->pivot->cantidad ?: 1;
                    $precioUnitario = $insumoRel->pivot->precio_unitario ?? 0;
                    $subtotalItem = $insumoRel->pivot->subtotal ?? ($precioUnitario * $cantidad);
                    $detallePrecio = ($precioUnitario * $cantidad) * 2;

                    $detalles[] = [
                        'descripcion' => $insumoRel->nombre,
                        'cantidad' => $cantidad,
                        'area' => '',
                        'tela' => $insumoRel->tipoInsumo?->nombre ?? '',
                        'tipo_cortina' => '',
                        'precio' => $detallePrecio
                    ];
                }
            @endphp

            @foreach($detalles as $detalle)
                @php
                    $subtotal += $detalle['precio'] ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $rowNum }}</td>
                    <td>{{ strtoupper($detalle['descripcion']) }}</td>
                    <td class="text-center">{{ $detalle['cantidad'] }}</td>
                    <td class="text-center">{{ strtoupper($detalle['area']) }}</td>
                    <td>{{ strtoupper($detalle['tela']) }}</td>
                    <td>{{ strtoupper($detalle['tipo_cortina']) }}</td>
                    <td class="text-right">${{ number_format($detalle['precio'] ?? 0, 2) }}</td>
                </tr>
                @php $rowNum++; @endphp
            @endforeach

            @if(count($detalles) < 5)
                @for($i = count($detalles); $i < 5; $i++)
                    <tr>
                        <td class="text-center">{{ $rowNum }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"></td>
                    </tr>
                    @php $rowNum++; @endphp
                @endfor
            @endif
        </tbody>
        <tfoot>
            @php
                $discountPercentage = $cotizacion->descuento ?? 0;
                $totalCalculated = 0;
                if($cotizacion->detalleCotizacion) {
                    $detalleC = $cotizacion->detalleCotizacion;
                    $totalCalculated = ((($detalleC->total_tela_final ?? 0) + ($detalleC->total_tergal_final ?? 0) + ($detalleC->total_final_forro ?? 0) + ($detalleC->costo_total_mano_obra ?? 0) + (($detalleC->cortinero_cantidad ?? 0) * ($detalleC->cortinero_precio ?? 0)) + (($detalleC->cortinero_tergal_cantidad ?? 0) * ($detalleC->cortinero_tergal_precio ?? 0))) * 2);
                }
                // Base for public price is subtotal; apply percentual discount if present
                $precioPublicoBase = $subtotal;
                $descuentoMonto = 0;
                if($discountPercentage && $discountPercentage > 0) {
                    $descuentoMonto = $precioPublicoBase * ($discountPercentage / 100);
                }
                $precioPublico = $precioPublicoBase - $descuentoMonto;
            @endphp
            <tr>
                <td colspan="6" class="text-right"><strong>SUBTOTAL</strong></td>
                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($discountPercentage && $discountPercentage > 0)
                <tr style="background-color: #f8d7da;">
                    <td colspan="6" class="text-right"><strong>DESCUENTO ({{ number_format($discountPercentage, 2) }}%)</strong></td>
                    <td class="text-right">-${{ number_format($descuentoMonto, 2) }}</td>
                </tr>
            @endif
            <!-- <tr style="background-color: #d4edda;">
                <td colspan="6" class="text-right"><strong>TOTAL CALCULADO</strong></td>
                <td class="text-right"><strong>${{ number_format($totalCalculated, 2) }}</strong></td>
            </tr> -->
            <tr>
                <td colspan="6" class="text-right"><strong>PRECIO PÚBLICO</strong></td>
                <td class="text-right">${{ number_format($precioPublico, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 10px; font-size: 9px; font-style: italic;">
        *COTIZACION SUJETA A CAMBIOS EN LAS MEDIDAS ENVIADAS
    </div>

    <div class="section-title">TERMINOS Y CONDICIONES</div>
    
    <div style="margin-bottom: 10px; font-size: 10px;">
        <strong>TRABAJOS GARANTIZADOS</strong><br>
        <strong>EXPERIENCIA 20 AÑOS</strong><br>
        <strong>NUESTROS TRABAJOS NO TIENEN COMPARACION</strong>
    </div>

    <div class="section-title">OBSERVACIONES</div>
    <div style="min-height: 40px; border: 1px solid #ccc; padding: 5px; font-size: 10px;">
        &nbsp;
    </div>

    <div class="section-title">RECIBO</div>
    <table>
        <tr>
            <td colspan="2" class="no-border" style="padding-bottom: 8px;">
                <strong>RECIBÍ LA CANTIDAD DE $</strong> 
                <span style="border-bottom: 1px solid #000; display: inline-block; width: 100px; text-align: center;">
                    {{ $cotizacion->anticipo ?? '' }}
                </span>
            </td>
            <td class="no-border" style="padding-bottom: 8px;">
                <strong>POR CONCEPTO DE</strong>
                [ ] ANT &nbsp;&nbsp;&nbsp;&nbsp; [ ] TOTAL
            </td>
        </tr>
        <tr>
            <td colspan="2" class="no-border" style="padding-bottom: 8px;">
                <strong>RESTANDO LA CANTIDAD DE $</strong> 
                <span style="border-bottom: 1px solid #000; display: inline-block; width: 100px; text-align: center;">
                    {{ $cotizacion->restante ?? '' }}
                </span>
            </td>
            <td class="no-border" style="padding-bottom: 8px;">
                <strong>FIRMA</strong> 
                <span style="border-bottom: 1px solid #000; display: inline-block; width: 100px;"></span>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="no-border">
                <strong>NOMBRE DEL CLIENTE</strong> 
                <span style="border-bottom: 1px solid #000; display: inline-block; width: 150px;">
                    {{ $cotizacion->cliente->nombre ?? '' }}
                </span>
            </td>
            <td class="no-border">
                <strong>FIRMA DEL CLIENTE</strong> 
                <span style="border-bottom: 1px solid #000; display: inline-block; width: 100px;"></span>
            </td>
        </tr>
    </table>

    <p style="margin-top: 10px; font-size: 11px;">
        Si usted tiene alguna duda sobre esta cotización, por favor, póngase en contacto con nosotros.
    </p>
</body>
</html>
