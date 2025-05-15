@extends('layouts.stisla')

@section('title', 'Existencias en ' . $almacen->nombre)

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Existencias en {{ $almacen->nombre }}</h1>
        <a href="{{ route('admin.almacenes.index') }}" class="btn btn-secondary ml-auto">Regresar</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Listado de Productos/Insumos</h4>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Producto/Insumo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($existencias as $existencia)
                        <tr>
                            <td>{{ $existencia->id_producto ?? $existencia->id_insumo }}</td>
                            <td>{{ $existencia->cantidad }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">No hay existencias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection