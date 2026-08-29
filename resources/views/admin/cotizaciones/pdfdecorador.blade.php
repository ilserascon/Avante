<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización #{{ $cotizacion->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1a8683;
            margin: 0;
            padding: 18px 22px;
            line-height: 1.4;
        }

        .pdf-header {
            width: 100%;
            margin-bottom: 18px;
            border-bottom: 3px solid #1a8683;
            padding-bottom: 14px;
            text-align: center;
        }

        .doc-title-center {
            font-size: 22px;
            font-weight: bold;
            color: #1a8683;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .doc-number-center {
            font-size: 13px;
            color: #145f5d;
            font-weight: bold;
        }

        .info-box {
            width: 100%;
            margin-bottom: 16px;
            background: #f5faf9;
        }

        .info-box td {
            padding: 8px 12px;
            border: 1px solid #dcefed;
            vertical-align: top;
        }

        .info-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5a8a88;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .info-value {
            color: #26344d;
        }

        .section-title {
            background: #1a8683;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 7px 12px;
            margin: 14px 0 0 0;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .detail-table thead th {
            background: #e8f5f4;
            color: #1a8683;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            border: 1px solid #b8ddd9;
            text-align: center;
        }

        .detail-table tbody td {
            padding: 7px 6px;
            border: 1px solid #dde6f0;
            font-size: 9.5px;
            vertical-align: middle;
        }

        .detail-table tbody tr:nth-child(even) {
            background: #f7fcfb;
        }

        .detail-table tfoot td {
            padding: 7px 8px;
            border: 1px solid #b8ddd9;
            font-size: 10px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        .total-row {
            background: #e8f5f4;
            font-weight: bold;
            color: #1a8683;
        }

        .grand-total-row {
            background: #1a8683;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }

        .grand-total-row td {
            border-color: #1a8683 !important;
            color: #fff !important;
        }

        .note-box {
            margin-top: 10px;
            padding: 8px 10px;
            background: #fff9e6;
            border-left: 3px solid #f0b429;
            font-size: 8.5px;
            font-style: italic;
            color: #7a6220;
        }

        .terms-box {
            padding: 10px 12px;
            border: 1px solid #dde6f0;
            background: #f7fcfb;
            font-size: 9px;
        }

        .terms-list {
            margin: 0;
            padding-left: 16px;
        }

        .terms-list li {
            margin-bottom: 4px;
        }

        .receipt-box {
            width: 100%;
            border: 1px solid #dde6f0;
            background: #f7fcfb;
            margin-top: 0;
        }

        .receipt-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-grid td {
            padding: 10px 12px;
            border: 1px solid #dcefed;
            vertical-align: top;
        }

        .receipt-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #5a8a88;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .receipt-field {
            min-height: 16px;
            border-bottom: 1px solid #1a8683;
            padding: 4px 2px 6px 2px;
            font-size: 10px;
            color: #26344d;
        }

        .receipt-field-empty {
            min-height: 16px;
            border-bottom: 1px solid #1a8683;
        }

        .receipt-field-money {
            min-height: 28px;
            border-bottom: 1px solid #1a8683;
            padding: 4px 2px 6px 2px;
            font-size: 10px;
            color: #26344d;
            font-weight: 600;
        }

        .receipt-options {
            font-size: 9.5px;
            color: #26344d;
            padding-top: 4px;
        }

        .receipt-checkbox {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #1a8683;
            margin-right: 4px;
            vertical-align: middle;
        }

        .footer-note {
            margin-top: 14px;
            font-size: 9px;
            color: #5a6b7d;
            text-align: center;
            border-top: 1px solid #dde6f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
@php
    $fmtMoney = function ($value) {
        return '$' . number_format((float) ($value ?? 0), 2);
    };

    $describirCatalogo = function ($nombre, $medida) {
        $nombre = trim((string) $nombre);
        $medida = trim((string) $medida);

        if ($medida === '' || mb_stripos($nombre, $medida) !== false) {
            return $nombre;
        }

        return $nombre === '' ? $medida : $nombre . ' - ' . $medida;
    };

    $calcularCostoCortinaDetalle = function ($detalle) {
        $costo = (float) ($detalle->costo_cortina ?? 0);
        if ($costo > 0) {
            return $costo;
        }

        $materiales =
            ((float) ($detalle->cortinero_cantidad ?? 0) * (float) ($detalle->cortinero_precio ?? 0)) +
            ((float) ($detalle->cortinero_tergal_cantidad ?? 0) * (float) ($detalle->cortinero_tergal_precio ?? 0));

        return (float) ($detalle->costo_total_tela_tergal_forro ?? 0) +
            (float) ($detalle->costo_total_mano_obra ?? 0) +
            $materiales;
    };

    $lineas = [];

    foreach ($detalles as $detalle) {
        $costoCortina = $calcularCostoCortinaDetalle($detalle);
        $decoradorPct = (float) ($detalle->decorador_porcentaje ?? 15);
        $precioBruto = $costoCortina * (1 + $decoradorPct / 100);
        $descuentoPct = (float) ($detalle->descuento ?? $cotizacion->descuento ?? 0);
        $descuentoLinea = $descuentoPct > 0 ? $precioBruto * ($descuentoPct / 100) : 0;
        $precioNeto = $precioBruto - $descuentoLinea;

        $nombresTelas = [];
        if ($detalle->lleva_cortina) {
            $nombresTelas[] = $detalle->tela?->nombre ?? $detalle->descripcion_tela;
        }
        if ($detalle->lleva_tergal) {
            $nombresTelas[] = $detalle->tergal?->nombre ?? $detalle->descripcion_tergal;
        }
        if ($detalle->lleva_forro) {
            $nombresTelas[] = $detalle->forro?->nombre ?? $detalle->descripcion_forro;
        }

        $lineas[] = [
            'descripcion' => $detalle->descripcion ?: 'Cortina / Tergal / Forro',
            'cantidad' => 1,
            'area' => $detalle->area ?? '',
            'tipo' => implode(' / ', array_filter($nombresTelas)) ?: '-',
            'descuento' => $descuentoPct > 0 ? number_format($descuentoPct, 2) . '%' : '-',
            'precio_unitario' => $precioBruto,
            'precio' => $precioNeto,
        ];
    }

    foreach ($cotizacion->insumos as $insumo) {
        $cantidad = (float) ($insumo->pivot->cantidad ?? 0);
        $precioUnit = (float) ($insumo->costo ?? 0);
        if ($precioUnit <= 0) {
            $precioUnit = (float) ($insumo->pivot->precio_unitario ?? 0);
        }
        $descuentoPct = (float) ($insumo->pivot->descuento ?? 0);
        $bruto = $cantidad * $precioUnit;
        $subtotal = $descuentoPct > 0 ? $bruto * (1 - $descuentoPct / 100) : $bruto;

        $lineas[] = [
            'descripcion' => $describirCatalogo($insumo->nombre, $insumo->medidaMostrar()),
            'cantidad' => $cantidad > 0 ? rtrim(rtrim(number_format($cantidad, 2), '0'), '.') : 1,
            'area' => '',
            'tipo' => $insumo->tipoInsumo?->nombre ?? 'Insumo',
            'descuento' => $descuentoPct > 0 ? number_format($descuentoPct, 2) . '%' : '-',
            'precio_unitario' => $precioUnit,
            'precio' => $subtotal,
        ];
    }

    foreach ($cotizacion->productos as $producto) {
        $cantidad = (float) ($producto->pivot->cantidad ?? 0);
        $precioUnit = (float) ($producto->precio ?? 0);
        if ($precioUnit <= 0) {
            $precioUnit = (float) ($producto->pivot->precio_unitario ?? 0);
        }
        $descuentoPct = (float) ($producto->pivot->descuento ?? 0);
        $bruto = $cantidad * $precioUnit;
        $subtotal = $descuentoPct > 0 ? $bruto * (1 - $descuentoPct / 100) : $bruto;

        $lineas[] = [
            'descripcion' => $describirCatalogo($producto->nombre, $producto->medidaMostrar()),
            'cantidad' => $cantidad > 0 ? rtrim(rtrim(number_format($cantidad, 2), '0'), '.') : 1,
            'area' => '',
            'tipo' => $producto->tipoProducto?->nombre ?? 'Producto',
            'descuento' => $descuentoPct > 0 ? number_format($descuentoPct, 2) . '%' : '-',
            'precio_unitario' => $precioUnit,
            'precio' => $subtotal,
        ];
    }

    $subtotalNeto = collect($lineas)->sum(fn ($linea) => (float) ($linea['precio'] ?? 0));
    $ivaMonto = $cotizacion->aplicar_iva ? $subtotalNeto * 0.16 : 0;
    $precioDecorador = $subtotalNeto + $ivaMonto;
@endphp

    <div class="pdf-header">
        <div class="doc-title-center">Cotización</div>
        <div class="doc-number-center">No. {{ str_pad($cotizacion->id, 5, '0', STR_PAD_LEFT) }}</div>
    </div>

    <table class="info-box">
        <tr>
            <td style="width: 50%;">
                <div class="info-label">Cliente</div>
                <div class="info-value">{{ $cotizacion->cliente?->nombre ?? '-' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="info-label">Fecha</div>
                <div class="info-value">{{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="info-label">Creado por</div>
                <div class="info-value">{{ $cotizacion->creadoPor?->name ?? 'Usuario no registrado' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Detalle de la cotización</div>
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 24%;">Descripción</th>
                <th style="width: 6%;">Cant.</th>
                <th style="width: 10%;">Área</th>
                <th style="width: 19%;">Tipo</th>
                <th style="width: 7%;">Desc.</th>
                <th style="width: 15%;">P. Unitario</th>
                <th style="width: 15%;">Precio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lineas as $index => $linea)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $linea['descripcion'] }}</td>
                    <td class="text-center">{{ $linea['cantidad'] }}</td>
                    <td class="text-center">{{ $linea['area'] ?: '-' }}</td>
                    <td>{{ $linea['tipo'] }}</td>
                    <td class="text-center">{{ $linea['descuento'] ?? '-' }}</td>
                    <td class="text-right">{{ $fmtMoney($linea['precio_unitario'] ?? 0) }}</td>
                    <td class="text-right text-bold">{{ $fmtMoney($linea['precio']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 16px; color: #6b7b95;">
                        Sin conceptos registrados en esta cotización.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Subtotal</td>
                <td class="text-right">{{ $fmtMoney($subtotalNeto) }}</td>
            </tr>
            @if($cotizacion->aplicar_iva)
                <tr class="total-row">
                    <td colspan="7" class="text-right">IVA (16%)</td>
                    <td class="text-right">{{ $fmtMoney($ivaMonto) }}</td>
                </tr>
            @endif
            <tr class="grand-total-row">
                <td colspan="7" class="text-right">PRECIO DECORADOR</td>
                <td class="text-right">{{ $fmtMoney($precioDecorador) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="note-box">
        * Cotización sujeta a cambios en las medidas enviadas. Vigencia sujeta a disponibilidad de materiales.
    </div>

    <div class="section-title">Términos y condiciones</div>
    <div class="terms-box">
        <ol class="terms-list">
            <li>Se requiere el % de anticipo y el resto al instalar.</li>
            <li>Instalación incluida en nuestros trabajos.</li>
            <li>Tiempo de entrega es de ___ días hábiles posteriores al anticipo.</li>
            <li>En compras diferidas a meses sin intereses se realiza en una sola exhibición.</li>
            <li>Si por algún motivo la instalación es aplazada por motivos ajenos a nosotros, el cliente deberá liquidar el resto de la cotización.</li>
        </ol>
    </div>

    <div class="section-title">Recibo</div>
    <div class="receipt-box">
        <table class="receipt-grid">
            <tr>
                <td style="width: 35%;">
                    <div class="receipt-label">Recibí la cantidad de</div>
                    <div class="receipt-field receipt-field-money">
                        @if($cotizacion->anticipo)
                            {{ $fmtMoney($cotizacion->anticipo) }}
                        @else
                            &nbsp;
                        @endif
                    </div>
                </td>
                <td style="width: 35%;">
                    <div class="receipt-label">Restando la cantidad de</div>
                    <div class="receipt-field receipt-field-money">
                        @if($cotizacion->restante)
                            {{ $fmtMoney($cotizacion->restante) }}
                        @else
                            &nbsp;
                        @endif
                    </div>
                </td>
                <td style="width: 30%;">
                    <div class="receipt-label">Por concepto de</div>
                    <div class="receipt-options">
                        <span class="receipt-checkbox"></span> Anticipo &nbsp;&nbsp;
                        <span class="receipt-checkbox"></span> Total
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="receipt-label">Nombre del cliente</div>
                    <div class="receipt-field">{{ $cotizacion->cliente?->nombre ?? '' }}</div>
                </td>
                <td>
                    <div class="receipt-label">Firma del asesor</div>
                    <div class="receipt-field-empty">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="receipt-label">Firma del cliente</div>
                    <div class="receipt-field-empty">&nbsp;</div>
                </td>
                <td>
                    <div class="receipt-label">Fecha de recepción</div>
                    <div class="receipt-field-empty">&nbsp;</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        Si usted tiene alguna duda sobre esta cotización, por favor póngase en contacto con nosotros.
    </div>
</body>
</html>
