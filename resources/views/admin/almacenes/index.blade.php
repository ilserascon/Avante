@extends('layouts.stisla')

@section('title', 'Almacenes')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Almacenes</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.almacenes.create') }}" class="btn btn-primary">Nuevo almacén</a>
        </div>
    </div>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Lista de Almacenes</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($almacenes as $almacen)
                            <tr>
                                <td>{{ $almacen->nombre }}</td>
                                <td>{{ $almacen->ubicacion }}</td>
                                <td>
                                    <a href="{{ route('admin.almacenes.existencia', $almacen->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Ver Existencia
                                    </a>
                                    <a href="{{ route('admin.almacenes.edit', $almacen->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay almacenes registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
