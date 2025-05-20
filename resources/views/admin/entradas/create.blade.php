@extends('layouts.stisla')

@section('title', 'Nueva Entrada')

@section('content')
<div class="section">
  <div class="section-header">
    <h1>Nueva Entrada</h1>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.entradas.store') }}">
          @csrf

          <div class="form-group">
            <label for="id_almacen">Almacén</label>
            <select name="id_almacen" class="form-control @error('id_almacen') is-invalid @enderror" required>
              @foreach($almacenes as $almacen)
                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
              @endforeach
            </select>
            @error('id_almacen') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div id="items-container">
            <!-- Productos/Insumos agregados dinámicamente -->
          </div>

          <div class="form-group d-flex gap-2 mb-4">
            <button type="button" id="add-product" class="btn btn-outline-secondary">Agregar Producto</button>
            <button type="button" id="add-insumo" class="btn btn-outline-secondary">Agregar Insumo</button>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('admin.entradas.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
let index = 0;

document.getElementById('add-product').onclick = function() {
  let html = `
    <div class="form-row align-items-end mb-3">
      <div class="col-md-6">
        <label>Producto</label>
        <select name="items[${index}][id_producto]" class="form-control" required>
          <option value="">Seleccione un producto...</option>
          @foreach($productos as $producto)
            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label>Cantidad</label>
        <input type="number" name="items[${index}][cantidad]" class="form-control" placeholder="Cantidad" required>
      </div>
    </div>`;
  document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
  index++;
};

document.getElementById('add-insumo').onclick = function() {
  let html = `
    <div class="form-row align-items-end mb-3">
      <div class="col-md-6">
        <label>Insumo</label>
        <select name="items[${index}][id_insumo]" class="form-control" required>
          <option value="">Seleccione un insumo...</option>
          @foreach($insumos as $insumo)
            <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label>Cantidad</label>
        <input type="number" name="items[${index}][cantidad]" class="form-control" placeholder="Cantidad" required>
      </div>
    </div>`;
  document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
  index++;
};
</script>
@endsection
