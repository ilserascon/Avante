@extends('layouts.stisla')

@section('title', 'Tipos de Producto')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Tipos de Producto</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.tipo-productos.create') }}" class="btn btn-primary">Nuevo Tipo de Producto</a>
        </div>
    </div>

    <div class="section-body">
        <form method="GET" action="{{ route('admin.tipo-productos.index') }}" class="form-inline mb-3">
            <div class="form-group mr-2">
                <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre" value="{{ request('nombre') }}">
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tipoProductos as $tipoProducto)
                            <tr>
                                <td>{{ $tipoProducto->nombre }}</td>
                                <td>{{ $tipoProducto->descripcion }}</td>
                                <td>
                                    <a href="{{ route('admin.tipo-productos.edit', $tipoProducto->id) }}" class="btn btn-warning btn-sm">Editar</a>
                                </td>
                            </tr>
                        @endforeach

                        @if ($tipoProductos->isEmpty())
                            <tr>
                                <td colspan="3" class="text-center">No se encontraron tipos de producto.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $tipoProductos->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
