@extends('layouts.stisla')

@section('title', 'Productos')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Productos</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.productos.create') }}" class="btn btn-primary">Nuevo Producto</a>
        </div>
    </div>

  <div class="section-body">
    <form method="GET" action="{{ route('admin.productos.index') }}" class="form-inline mb-3">
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
              <th>Insumos</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($productos as $producto)
              <tr>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->descripcion }}</td>
                <td>
                  <a href="{{ route('admin.productos.insumos', $producto->id) }}" class="btn btn-info btn-sm">Ver Insumos</a>
                </td>
                <td>
                  <a href="{{ route('admin.productos.edit', $producto->id) }}" class="btn btn-warning btn-sm">Editar</a>
                </td>
              </tr>
            @endforeach

            @if ($productos->isEmpty())
              <tr>
                <td colspan="4" class="text-center">No se encontraron productos.</td>
              </tr>
            @endif
          </tbody>
        </table>
        <div class="d-flex justify-content-center mt-3">
          {{ $productos->appends(request()->query())->links('pagination::bootstrap-4') }}                
      </div>
  </div>
</div>
@endsection