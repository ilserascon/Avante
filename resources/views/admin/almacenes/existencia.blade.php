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

                {{-- Filtros --}}
                <form method="GET" class="mb-4">
                    <div class="form-row">
                        <div class="col-md-3">
                            <label>Producto</label>
                            <input type="text" name="producto" class="form-control" value="{{ request('producto') }}" placeholder="Nombre del producto">
                        </div>
                        <div class="col-md-3">
                            <label>Insumo</label>
                            <input type="text" name="insumo" class="form-control" value="{{ request('insumo') }}" placeholder="Nombre del insumo">
                        </div>
                        <div class="col-md-2">
                            <label>Existencia</label>
                            <input type="number" name="existencia" class="form-control" value="{{ request('existencia') }}" placeholder="Cantidad">
                        </div>
                        <div class="col-md-2">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ request('nombre') }}" placeholder="Buscar por nombre">
                        </div>
                        <div class="col-md-2">
                            <label>Ver</label>
                            <select name="tipo" class="form-control" onchange="this.form.submit()">
                                <option value="">Seleccione una tabla</option>
                                <option value="producto" {{ request('tipo') == 'producto' ? 'selected' : '' }}>Solo productos</option>
                                <option value="insumo" {{ request('tipo') == 'insumo' ? 'selected' : '' }}>Solo insumos</option>
                            </select>
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class="btn btn-primary btn-block">Filtrar</button>
                        </div>
                    </div>
                </form>

                @if(request('tipo') == 'producto')
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad Producto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($existencias as $fila)
                                @if(isset($fila['producto']) && $fila['producto'] !== '-')
                                    <tr>
                                        <td>{{ $fila['producto'] }}</td>
                                        <td>{{ $fila['cantidad_producto'] }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No hay productos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif(request('tipo') == 'insumo')
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Insumo</th>
                                <th>Cantidad Insumo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($existencias as $fila)
                                @if(isset($fila['insumo']) && $fila['insumo'] !== '-')
                                    <tr>
                                        <td>{{ $fila['insumo'] }}</td>
                                        <td>{{ $fila['cantidad_insumo'] }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No hay insumos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-info mt-4">
                        Selecciona una tabla para ver los datos.
                    </div>
                @endif

                <div class="d-flex justify-content-center mt-3">
                    {{ $existencias->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection