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

          <!-- Campo Nombre -->
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
            @error('nombre') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Campo Descripción -->
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
            @error('descripcion') 
              <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
          </div>

          <!-- Campo Insumos -->
          <div class="form-group">
            <label for="insumos">Insumos</label>
            <select name="insumos[]" class="form-control insumo-select" multiple required>
              <option value="" disabled>Seleccione o busque un insumo</option>
              @foreach ($insumos as $insumo)
                <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
              @endforeach
            </select>
            @error('insumos')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <!-- Botones -->
          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Agregar CSS de Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<!-- Agregar JS de jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Agregar JS de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    // Inicializar Select2 para el campo de selección de insumos
    $('.insumo-select').select2({
      placeholder: 'Seleccione o busque un insumo',
      width: '100%',
      allowClear: true,
      tags: false, // No permitir la creación de nuevas opciones
      minimumResultsForSearch: 1, // Mostrar el filtro de búsqueda
    });
  });
</script>
@endsection
