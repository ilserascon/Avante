@extends('layouts.stisla')

@section('title', 'Cotización Simulada')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Cotización Simulada</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $cliente->nombre }}</p>
                <p><strong>RFC:</strong> {{ $cliente->rfc }}</p>
                <p><strong>Razón Social:</strong> {{ $cliente->razon_social }}</p>
                <p><strong>Email:</strong> {{ $cliente->email ?? 'No registrado' }}</p>

                <hr>

                <p>Gracias por tu preferencia. Esta es una cotización simulada.</p>
                <ul>
                    <li>Producto A: $500.00</li>
                    <li>Producto B: $1,200.00</li>
                </ul>

                <p><strong>Total: $1,700.00 MXN</strong></p>
            </div>
        </div>
    </div>
</div>
@endsection
