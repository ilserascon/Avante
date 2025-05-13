@extends('layouts.stisla')

@section('title', 'Clientes')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Clientes</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary">Nuevo Cliente</a>
        </div>
    </div>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.clientes.index') }}" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col">
                    <input type="text" name="rfc" value="{{ request('rfc') }}" class="form-control" placeholder="Buscar por RFC">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h4>Lista de clientes</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Razón Social</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Código Postal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->rfc }}</td>
                            <td>{{ $cliente->razon_social }}</td>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                            <td>{{ $cliente->email ?? '-' }}</td>
                            <td>{{ $cliente->direccion ?? '-' }}</td>
                            <td>{{ $cliente->codigo_postal ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.clientes.edit', $cliente->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.clientes.destroy', $cliente->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar Cliente?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                @php
                                    $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono ?? '');
                                    $urlCotizacion = url("/admin/clientes/{$cliente->id}/cotizacion-simulada");
                                    $mensaje = urlencode("Hola {$cliente->nombre}, te comparto el enlace con tu cotización simulada: {$urlCotizacion}. Haz clic para verla!");
                                @endphp

                                @if($telefono)
                                    <a href="https://wa.me/52{{ $telefono }}?text={{ $mensaje }}" target="_blank" 
                                    class="btn btn-success btn-sm mt-1" 
                                    style="background-color: #25D366; border-color: #25D366;">
                                        <i class="fab fa-whatsapp"></i> Cotización
                                    </a>
                                @endif


                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No hay clientes registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
