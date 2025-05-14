@extends('layouts.stisla')

@section('title', 'Entradas')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Entradas</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.entradas.create') }}" class="btn btn-primary">Nueva Entrada</a>
        </div>
    </div>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Lista de Entradas</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Almacén</th>
                            <th>Usuario</th>
                            <th>Fecha</th>
                            <th>Acciones</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entradas as $entrada)
                            <tr>
                                <td>{{ $entrada->id }}</td>
                                <td>{{ $entrada->almacen->nombre ?? 'N/A' }}</td> <!-- Nombre del almacén -->
                                <td>{{ $entrada->usuario->name ?? 'N/A' }}</td> <!-- Nombre del usuario -->
                                <td>{{ $entrada->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.entradas.edit', $entrada->id) }}" class="btn btn-warning btn-sm">
                                        Editar
                                    <a href="{{ route('admin.entradas.show', $entrada->id) }}" class="btn btn-info btn-sm">
                                        Ver Detalles
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
