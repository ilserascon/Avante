@extends('layouts.stisla')

@section('title', 'Existencia en ' . $almacen->nombre)

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Existencia en {{ $almacen->nombre }}</h1>
        <a href="{{ route('admin.almacenes.index') }}" class="btn btn-secondary ml-auto">Volver</a>
    </div>
    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Productos e Insumos en este almacén</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Insumo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($existencias as $existencia)
                            <tr>
                                <td>{{ $existencia->producto->nombre ?? '-' }}</td>
                                <td>{{ $existencia->insumo->nombre ?? '-' }}</td>
                                <td>{{ $existencia->cantidad }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay existencias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
               <div class="d-flex justify-content-center mt-3">
                    {{ $existencias->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection