@extends('layouts.stisla')

@section('title', 'Nuevo Producto')

@section('content')
<div class="section">
  <div class="section-header">
    <h1>Nuevo Producto</h1>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.productos.store') }}">
          @csrf

          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label>Insumos</label>
            <button type="button" class="btn btn-info btn-sm mb-2" id="add-insumo">Agregar Insumo</button>
            <div id="insumos-container">
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('add-insumo').addEventListener('click', function () {
    const container = document.getElementById('insumos-container');
    const insumoIndex = container.children.length;

    const insumoDiv = document.createElement('div');
    insumoDiv.classList.add('form-row', 'align-items-center', 'mb-2');

    insumoDiv.innerHTML = `
      <div class="col-md-5">
        <select name="insumos[${insumoIndex}][id]" class="form-control" required>
          <option value="">Seleccione un insumo</option>
          @foreach ($insumos as $insumo)
            <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <input type="number" name="insumos[${insumoIndex}][cantidad]" class="form-control" min="0" step="0.01" placeholder="Cantidad" required>
      </div>
      <div class="col-md-3">
        <button type="button" class="btn btn-danger btn-sm remove-insumo">Eliminar</button>
      </div>
    `;

    container.appendChild(insumoDiv);

    insumoDiv.querySelector('.remove-insumo').addEventListener('click', function () {
      insumoDiv.remove();
    });
  });
</script>
@endsection
