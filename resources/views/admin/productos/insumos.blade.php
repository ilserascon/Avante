@extends('layouts.stisla')

@section('title', 'Insumos del Producto')

@section('content')
<div class="section">
  <div class="section-header">
    <h1>Insumos de {{ $producto->nombre }}</h1>
  </div>
</div>

  <div class="section-body">
    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary mb-3">Volver al listado</a>

    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Nombre del Insumo</th>
              <th>Cantidad Asignada</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($producto->insumos as $insumo)
            <tr>
                <td>{{ $insumo->nombre_completo }}</td> 
                <td>{{ $insumo->pivot->cantidad }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center">No hay insumos asignados a este producto.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
