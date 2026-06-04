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
                $decoradorPorcentaje = 0;

                if($cotizacion->detalleCotizacion) {
                    $detalleC = $cotizacion->detalleCotizacion;
                    $decoradorPorcentaje = $detalleC->decorador_porcentaje ?? 0;
                    $decoradorFactor = 1 + ($decoradorPorcentaje / 100);

                    $insumoTela = $detalleC->tela_id ? \App\Models\Insumo::find($detalleC->tela_id) : null;
                    $insumoTergal = $detalleC->tergal_id ? \App\Models\Insumo::find($detalleC->tergal_id) : null;
                    $insumoForro = $detalleC->forro_id ? \App\Models\Insumo::find($detalleC->forro_id) : null;

                    $idsExcluidos = array_filter([$detalleC->tela_id, $detalleC->tergal_id, $detalleC->forro_id]);

                    if($detalleC->total_tela_final && $detalleC->total_tela_final > 0) {
                        $baseTela = ($detalleC->total_tela_final ?? 0) + ($detalleC->total_mano_obra_1 ?? 0) + (($detalleC->cortinero_cantidad ?? 0) + ($detalleC->cortinero_precio ?? 0));
                        $precioTela = $baseTela * $decoradorFactor;
                        $detalles[] = [
                            'descripcion' => 'CORTINA',
                            'cantidad' => 1,
                            'area' => $cotizacion->area ?? '',
                            'tela' => $insumoTela?->nombre ?? $detalleC->descripcion_tela ?? '',
                            'tipo_cortina' => $detalleC->tipo_cortina ?? '',
                            'precio' => $precioTela
                        ];
                    }
                    if($detalleC->total_tergal_final && $detalleC->total_tergal_final > 0) {
                        $baseTergal = ($detalleC->total_tergal_final ?? 0) + ($detalleC->total_mano_obra_2 ?? 0) + (($detalleC->cortinero_tergal_cantidad ?? 0) + ($detalleC->cortinero_tergal_precio ?? 0));
                        $precioTergal = $baseTergal * $decoradorFactor;
                        $detalles[] = [
                            'descripcion' => 'TERGAL',
                            'cantidad' => 1,
                            'area' => '',
                            'tela' => $insumoTergal?->nombre ?? $detalleC->descripcion_tergal ?? '',
                            'tipo_cortina' => '',
                            'precio' => $precioTergal
                        ];
                    }
                    if($detalleC->total_final_forro && $detalleC->total_final_forro > 0) {
                        $baseForro = $detalleC->total_final_forro ?? 0;
                        $precioForro = $baseForro * $decoradorFactor;
                        $detalles[] = [
                            'descripcion' => 'FORRO',
                            'cantidad' => 1,
                            'area' => '',
                            'tela' => $insumoForro?->nombre ?? $detalleC->descripcion_forro ?? '',
                            'tipo_cortina' => '',
                            'precio' => $precioForro
                        ];
                    }
                }

                foreach($cotizacion->insumos as $insumoRel) {
                    if(in_array($insumoRel->id, $idsExcluidos)) {
                        continue;
                    }

                    $cantidad = $insumoRel->pivot->cantidad ?: 1;
                    $precioUnitario = $insumoRel->pivot->precio_unitario ?? 0;
                    $baseInsumo = ($precioUnitario * $cantidad);
                    $detallePrecio = $baseInsumo * ($decoradorPorcentaje ? (1 + ($decoradorPorcentaje / 100)) : 1);

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
                $totalCalculated = 0;
                if($cotizacion->detalleCotizacion) {
                    $detalleC = $cotizacion->detalleCotizacion;
                    $decoradorPorcentaje = $detalleC->decorador_porcentaje ?? 0;
                    $decoradorFactor = 1 + ($decoradorPorcentaje / 100);
                    $baseTela = ($detalleC->total_tela_final ?? 0) + ($detalleC->total_mano_obra_1 ?? 0) + (($detalleC->cortinero_cantidad ?? 0) + ($detalleC->cortinero_precio ?? 0));
                    $baseTergal = ($detalleC->total_tergal_final ?? 0) + ($detalleC->total_mano_obra_2 ?? 0) + (($detalleC->cortinero_tergal_cantidad ?? 0) + ($detalleC->cortinero_tergal_precio ?? 0));
                    $baseForro = $detalleC->total_final_forro ?? 0;
                    $totalCalculated = ($baseTela + $baseTergal + $baseForro) * $decoradorFactor;
                }
            @endphp
            <tr>
                <td colspan="6" class="text-right"><strong>SUBTOTAL</strong></td>
                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><strong>PRECIO DECORADOR</strong></td>
                <td class="text-right">${{ number_format($cotizacion->costo_decorador ?? 0, 2) }}</td>
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

    @php
        // Insumos fijos por nombre
        $insumosFijos = ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'];
        // Buscar el cortinero dinámico (tipo 6) si existe
        $cortineroDinamico = $cotizacion->insumos->first(function($insumo) {
            return $insumo->id_tipo_insumo == 6;
        });
    @endphp
</body>
</html>
