@extends('layouts.stisla')

@section('title', 'Crear Entrada')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Crear Entrada</h1>
    </div>

    <div class="section-body">
        <form method="POST" action="{{ route('admin.entradas.store') }}">
            @csrf

            <div class="form-group">
                <label for="id_almacen">Almacén</label>
                <select name="id_almacen" class="form-control" required>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
            </div>

          <div id="productos-container">
              <h4>Productos</h4>
              <div class="form-group">
                  <label for="productos[0][id_producto]">Producto</label>
                  <select name="productos[0][id_producto]" class="form-control" required>
                      @foreach ($productos as $producto)
                          <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group">
                  <label for="productos[0][id_insumo]">Insumo</label>
                  <select name="productos[0][id_insumo]" class="form-control" required>
                      @foreach ($insumos as $insumo)
                          <option value="{{ $insumo->id }}">{{ $insumo->nombre }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group">
                  <label for="productos[0][cantidad]">Cantidad</label>
                  <input type="number" name="productos[0][cantidad]" class="form-control" required>
              </div>
              <div class="form-group">
                  <label for="productos[0][precio_unitario]">Precio Unitario</label>
                  <input type="number" step="0.01" name="productos[0][precio_unitario]" class="form-control" required>
              </div>
          </div>

            <button type="button" id="add-product" class="btn btn-secondary">Agregar Producto</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</div>

<script>
    let productIndex = 1;
    document.getElementById('add-product').addEventListener('click', function () {
        const container = document.getElementById('productos-container');
        const newProduct = `
            <div class="form-group">
                <label for="productos[${productIndex}][id_producto]">Producto</label>
                <select name="productos[${productIndex}][id_producto]" class="form-control" required>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="productos[${productIndex}][cantidad]">Cantidad</label>
                <input type="number" name="productos[${productIndex}][cantidad]" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="productos[${productIndex}][precio_unitario]">Precio Unitario</label>
                <input type="number" step="0.01" name="productos[${productIndex}][precio_unitario]" class="form-control" required>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newProduct);
        productIndex++;
    });
</script>
@endsection