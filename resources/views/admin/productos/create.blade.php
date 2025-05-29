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
              <!-- Aquí se agregarán los insumos -->
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Incluir CSS y JS de Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  // Opciones de insumos para agregar dinámicamente
  const insumosOptions = `
    <option value="">Seleccione un insumo</option>
    @foreach ($insumos as $insumo)
      <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
    @endforeach
  `;

  function initializeSelect2(element) {
    $(element).select2({
      placeholder: "Seleccione un insumo",
      width: '100%',
      allowClear: true,
      dropdownParent: $(element).parent()
    });
  }

  document.getElementById('add-insumo').addEventListener('click', function () {
    const container = document.getElementById('insumos-container');
    const insumoIndex = container.children.length;

    const insumoDiv = document.createElement('div');
    insumoDiv.classList.add('form-row', 'align-items-center', 'mb-2');

    insumoDiv.innerHTML = `
      <div class="col-md-5">
        <select name="insumos[${insumoIndex}][id]" class="form-control insumo-select" required>
          ${insumosOptions}
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

    // Inicializar Select2 en el nuevo select
    initializeSelect2(insumoDiv.querySelector('select'));

    // Evento para eliminar insumo
    insumoDiv.querySelector('.remove-insumo').addEventListener('click', function () {
      insumoDiv.remove();
    });
  });

  // Inicializar Select2 en selects existentes (por si hay alguno en el futuro)
  document.querySelectorAll('.insumo-select').forEach(function(select) {
    initializeSelect2(select);
  });
</script>
@endsection
