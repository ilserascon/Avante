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
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="w-50">Cliente:</th>
                                    <td>{{ $cotizacion->cliente ? $cotizacion->cliente->nombre : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Estatus:</th>
                                    <td>{{ ucfirst($cotizacion->estatus) }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>
                                        @php
                                            $tipos = [];
                                            if($cotizacion->lleva_cortina) $tipos[] = 'Cortina';
                                            if($cotizacion->lleva_tergal) $tipos[] = 'Tergal';
                                        @endphp
                                        {{ implode(', ', $tipos) ?: '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lleva Forro:</th>
                                    <td>{{ $cotizacion->lleva_forro ? 'Sí' : 'No' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="w-50">Total Lienzos:</th>
                                    <td>{{ $cotizacion->total_lienzos ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>M2 Tela:</th>
                                    <td>{{ $cotizacion->total_m2_tela ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>M2 Tergal:</th>
                                    <td>{{ $cotizacion->total_m2_tergal ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>M2 Forro:</th>
                                    <td>{{ $cotizacion->total_m2_forro ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Costo Cortina:</th>
                                    <td>${{ number_format($cotizacion->costo_cortina ?? 0, 2) }}</td>
                                </tr>
                                @if(auth()->user() && auth()->user()->role && auth()->user()->role->nombre === 'Administrador')
                                    <tr>
                                        <th>Utilidad:</th>
                                        <td>${{ number_format($cotizacion->utilidad ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Costo Decorador:</th>
                                        <td>${{ number_format($cotizacion->costo_decorador ?? 0, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Precio Público:</th>
                                    <td><strong>${{ number_format($cotizacion->precio_publico ?? 0, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Resumen de materiales eliminado a petición del usuario --}}
                <hr>
                <h5>Detalle de cotización</h5>
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
                            $precioTela = ($detalleC->total_tela_final + ($detalleC->total_mano_obra_1 ?? 0) + $cortineroSum) * 2;
                            $detalles[] = ['descripcion' => 'CORTINA', 'cantidad' => 1, 'area' => $cotizacion->area ?? '', 'tela' => $insumoTela?->nombre ?? $detalleC->descripcion_tela ?? '', 'tipo' => $detalleC->tipo_cortina ?? '', 'precio' => $precioTela];
                        }
                        if($detalleC->total_tergal_final && $detalleC->total_tergal_final > 0) {
                            $cortineroTergalSum = ($detalleC->cortinero_tergal_cantidad ?? 0) * ($detalleC->cortinero_tergal_precio ?? 0);
                            $precioTergal = ($detalleC->total_tergal_final + ($detalleC->total_mano_obra_2 ?? 0) + $cortineroTergalSum) * 2;
                            $detalles[] = ['descripcion' => 'TERGAL', 'cantidad' => 1, 'area' => '', 'tela' => $insumoTergal?->nombre ?? $detalleC->descripcion_tergal ?? '', 'tipo' => '', 'precio' => $precioTergal];
                        }
                        if($detalleC->total_final_forro && $detalleC->total_final_forro > 0) {
                            $precioForro = $detalleC->total_final_forro * 2;
                            $detalles[] = ['descripcion' => 'FORRO', 'cantidad' => 1, 'area' => '', 'tela' => $insumoForro?->nombre ?? $detalleC->descripcion_forro ?? '', 'tipo' => '', 'precio' => $precioForro];
                        }
                    }
                    foreach($cotizacion->insumos as $insumoRel) {
                        if(in_array($insumoRel->id, $idsExcluidos)) continue;
                        $cantidad = $insumoRel->pivot->cantidad ?: 1;
                        $precioUnitario = $insumoRel->pivot->precio_unitario ?? 0;
                        $detallePrecio = ($precioUnitario * $cantidad) * 2;
                        $detalles[] = ['descripcion' => $insumoRel->nombre, 'cantidad' => $cantidad, 'area' => '', 'tela' => $insumoRel->tipoInsumo?->nombre ?? '', 'tipo' => '', 'precio' => $detallePrecio];
                    }
                @endphp

                @if(count($detalles))
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th style="width:5%">U</th>
                                    <th style="width:20%">DESCRIPCIÓN</th>
                                    <th style="width:10%">CANTIDAD</th>
                                    <th style="width:15%">ÁREA</th>
                                    <th style="width:25%">TELAS</th>
                                    <th style="width:15%">TIPO</th>
                                    <th style="width:10%" class="text-right">PRECIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalles as $d)
                                    @php $subtotal += $d['precio'] ?? 0; @endphp
                                    <tr>
                                        <td class="text-center">{{ $rowNum }}</td>
                                        <td>{{ strtoupper($d['descripcion']) }}</td>
                                        <td class="text-center">{{ $d['cantidad'] }}</td>
                                        <td class="text-center">{{ strtoupper($d['area']) }}</td>
                                        <td>{{ strtoupper($d['tela']) }}</td>
                                        <td>{{ strtoupper($d['tipo']) }}</td>
                                        <td class="text-right">${{ number_format($d['precio'] ?? 0, 2) }}</td>
                                    </tr>
                                    @php $rowNum++; @endphp
                                @endforeach
                            </tbody>
                            @php
                                $discountPercentage = $cotizacion->descuento ?? 0;
                                $descuentoMonto = 0;
                                if($discountPercentage && $discountPercentage > 0) {
                                    $descuentoMonto = $subtotal * ($discountPercentage / 100);
                                }
                                $precioPublicoComputed = $cotizacion->precio_publico ?? ($subtotal - $descuentoMonto);
                            @endphp
                            <tfoot>
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
                                <tr>
                                    <td colspan="6" class="text-right"><strong>PRECIO PÚBLICO</strong></td>
                                    <td class="text-right">${{ number_format($precioPublicoComputed, 2) }}</td>
                                </tr>
                            </tfoot>
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
                            $telefono = 6623165287;
                            $urlPdf = asset('storage/pdfs/cotizacion_' . $cotizacion->id . '.pdf');
                            $mensaje = urlencode("Cotización para el Cliente: {$cliente->nombre}, \n{$urlPdf}");
                        @endphp
                        <a href="https://wa.me/52{{ $telefono }}?text={{ $mensaje }}" target="_blank"
                            class="btn btn-success btn-sm flex-fill"
                            style="background-color: #4aa46b; border-color: #4aa46b; max-width:200px;">
                            <i class="fab fa-whatsapp"></i> Enviar a WhatsApp Personal
                        </a>&nbsp;

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
                                <i class="fab fa-whatsapp"></i> Enviar por WhatsApp a Cliente
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
