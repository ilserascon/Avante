<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de taller #{{ $cotizacion->id }}</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #26344d;
            margin: 0;
            line-height: 1.35;
        }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .hoja-break { page-break-after: always; }

        .pdf-header { margin-bottom: 12px; border-bottom: 3px solid rgb(39, 172, 138); padding-bottom: 8px; }
        .pdf-header td { border: none; padding: 0; }
        .logo-cell { width: 40%; }
        .logo-cell img { width: 140px; }
        .doc-title { text-align: right; font-size: 16px; font-weight: bold; color: rgb(39, 172, 138); }
        .doc-number { text-align: right; font-size: 12px; font-weight: bold; color: rgb(39, 172, 138); margin-top: 2px; }
        .doc-partida { text-align: right; font-size: 11px; font-weight: bold; margin-top: 4px; }

        .info-box { margin-bottom: 10px; }
        .info-box td { padding: 6px 8px; border: 1px solid #dcefed; background: #f5faf9; }
        .lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        .val { color: #26344d; }

        .section-title {
            background: rgb(39, 172, 138);
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 8px;
            margin-top: 10px;
        }
        .grid td {
            border: 1px solid #dde6f0;
            padding: 5px 7px;
            width: 25%;
        }
        .grid tr:nth-child(even) td { background: #f7fcfb; }
        .alturas { width: 100%; margin-top: 3px; }
        .alturas th {
            width: 33%;
            background: #e8f5f4;
            color: #000;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            padding: 4px 6px;
            border: 1px solid #b8ddd9;
        }
        .alturas td {
            width: 33%;
            text-align: center;
            padding: 5px 6px;
            border: 1px solid #dde6f0;
            background: #fff;
        }
        .obs {
            border: 1px solid #dde6f0;
            padding: 7px 8px;
            min-height: 36px;
        }
        .pie {
            margin-top: 12px;
            font-size: 9px;
            color: #5a6b7d;
        }
    </style>
</head>
<body>
@php
    $texto = function ($value) {
        $valor = trim((string) ($value ?? ''));

        return $valor === '' ? '—' : $valor;
    };

    $numero = function ($value) {
        if ($value === null || $value === '') {
            return null;
        }

        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    };

    $anchoAMetros = function ($valor) use ($numero) {
        if ($valor === null || $valor === '') {
            return null;
        }

        $crudo = (float) $valor;

        return $numero($crudo <= 10 ? $crudo : $crudo / 100);
    };

    $medida = function ($ancho, $largo) {
        if ($ancho === null && $largo === null) {
            return '—';
        }

        return ($ancho ?? '—') . ' × ' . ($largo ?? '—') . ' m';
    };

    $metros = function ($value) use ($numero) {
        $valor = $numero($value);

        return $valor === null ? '—' : $valor . ' m';
    };

    $totalPartidas = $detalles->count();
    $folio = str_pad($cotizacion->id, 5, '0', STR_PAD_LEFT);
    $fecha = $cotizacion->orden_taller_fecha
        ? $cotizacion->orden_taller_fecha->format('d/m/Y')
        : now()->format('d/m/Y');
    $vendedor = $cotizacion->creadoPor?->name ?? '—';
    $cliente = $cotizacion->cliente;
@endphp

@foreach($detalles as $index => $detalle)
    <div class="{{ $loop->last ? '' : 'hoja-break' }}">
        <table class="pdf-header">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('stisla/assets/img/Logo.jpg') }}" alt="Avante Decoraciones">
                </td>
                <td>
                    <div class="doc-title">ORDEN DE TALLER</div>
                    <div class="doc-number">No. {{ $folio }}</div>
                    <div class="doc-partida">Partida {{ $index + 1 }} de {{ $totalPartidas }}</div>
                </td>
            </tr>
        </table>

        <table class="info-box">
            <tr>
                <td style="width: 46%;">
                    <div class="lbl">Cliente</div>
                    <div class="val">{{ $texto($cliente?->nombre) }}</div>
                    @if(trim((string) ($cliente?->telefono ?? '')) !== '')
                        <div class="val">{{ $cliente->telefono }}</div>
                    @endif
                    @if(trim((string) ($cliente?->direccion ?? '')) !== '')
                        <div class="val">{{ $cliente->direccion }}</div>
                    @endif
                </td>
                <td style="width: 18%;">
                    <div class="lbl">Fecha</div>
                    <div class="val">{{ $fecha }}</div>
                </td>
                <td style="width: 36%;">
                    <div class="lbl">Vendedor</div>
                    <div class="val">{{ $vendedor }}</div>
                </td>
            </tr>
        </table>

        @php
            $baston = $bastonesPorDetalle[$detalle->id] ?? ['numero' => null, 'medida' => null];
        @endphp
        <div class="section-title">Cortina</div>
        <table class="grid">
            <tr>
                <td colspan="2">
                    <div class="lbl">Área</div>
                    <div class="val">{{ $texto($detalle->area) }}</div>
                </td>
                <td colspan="2">
                    <div class="lbl">Medida de fabricación</div>
                    <div class="val">{{ $medida($anchoAMetros($detalle->ancho), $numero($detalle->largo)) }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="lbl">Techo / muro</div>
                    <div class="val">{{ $texto($detalle->techo_muro) }}</div>
                </td>
                <td>
                    <div class="lbl">Confección</div>
                    <div class="val">{{ $texto($detalle->confeccion) }}</div>
                </td>
                <td colspan="2">
                    <div class="lbl">Apertura</div>
                    <div class="val">{{ $texto($detalle->apertura) }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="lbl">Alturas originales (referencia)</div>
                    <table class="alturas">
                        <tr>
                            <th>Izq.</th>
                            <th>Centro</th>
                            <th>Derecho</th>
                        </tr>
                        <tr>
                            <td>{{ $metros($detalle->altura_izq) }}</td>
                            <td>{{ $metros($detalle->altura_centro) }}</td>
                            <td>{{ $metros($detalle->altura_der) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="lbl">Tela cortina</div>
                    <div class="val">{{ $texto($detalle->tela?->nombre ?: $detalle->descripcion_tela) }}</div>
                </td>
                <td colspan="2">
                    <div class="lbl">Color cortina</div>
                    <div class="val">{{ $texto($detalle->tela?->color) }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="lbl">Control cortina</div>
                    <div class="val">{{ $texto($detalle->control) }}</div>
                </td>
                <td colspan="2">
                    <div class="lbl">Cortinero cortina</div>
                    <div class="val">{{ $texto($detalle->cortinero?->nombre) }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="lbl">Número de bastones</div>
                    <div class="val">{{ $texto($baston['numero']) }}</div>
                </td>
                <td>
                    <div class="lbl">Medida de bastón</div>
                    <div class="val">{{ $texto($baston['medida']) }}</div>
                </td>
                <td>
                    <div class="lbl">Número de correderas</div>
                    <div class="val">{{ $texto($numeroCorrederas[$detalle->id] ?? null) }}</div>
                </td>
                <td>
                    <div class="lbl">Vuelta lateral</div>
                    <div class="val">{{ $numero($detalle->vuelta_lateral) === null ? '—' : $numero($detalle->vuelta_lateral) . ' cm' }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Observación</div>
        <div class="obs">{{ $texto($detalle->observacion_taller) }}</div>

        <div class="pie">Elaborado por: {{ $vendedor }}</div>
    </div>
@endforeach
</body>
</html>
