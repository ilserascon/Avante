@extends('layouts.stisla')

@section('title', 'Insumos')

@section('content')

<div class="section">
    <div class="section-header">
        <h1>Insumos</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.insumos.create') }}" class="btn btn-primary">Nuevo Insumo</a>
        </div>
    </div>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.insumos.index') }}" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col">
                    <select name="tipo_insumo" class="form-control">
                        <option value="">Seleccionar Tipo de Insumo</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ request('tipo_insumo') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="{{ route('admin.insumos.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h4>Lista de Insumos</h4>
            </div>
            <div class="card-body table-responsive">

            @if(!$tipoSeleccionado)
                <div class="text-center p-4">
                    <h5>Seleccione un tipo de insumo para ver sus campos.</h5>
                </div>
            @elseif($insumos->isEmpty())
                <div class="text-center p-4">
                    <h5>No hay insumos registrados para este tipo.</h5>
                </div>
            @else
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Proveedor</th>
                            <th>Costo</th>
                            <th>Precio Público</th>
                            <th>Utilidad</th>
                            @foreach($camposDinamicos as $campo => $valor)
                                <th>{{ $valor }}</th>
                            @endforeach
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($insumos as $insumo)
                            <tr>
                                <td>{{ $insumo->nombre }}</td>
                                <td>{{ $insumo->proveedor->nombre ?? 'N/A' }}</td>
                                <td>{{ $insumo->costo }}</td>
                                <td>{{ $insumo->precio_publico }}</td>
                                <td>{{ $insumo->utilidad }}</td>
                                @foreach($camposDinamicos as $campo => $valor)
                                    <td>{{ $insumo->$campo }}</td>
                                @endforeach
                                <td>
                                    <a href="{{ route('admin.insumos.edit', $insumo->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $insumos->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @endif

            </div>
        </div>
    </div>
</div>
@endsection
