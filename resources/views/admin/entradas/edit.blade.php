@extends('layouts.stisla')

@section('title', 'Editar Entrada')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Entrada</h1>
    </div>

    <div class="section-body">
        <form method="POST" action="{{ route('admin.entradas.update', $entrada->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="id_almacen">Almacén</label>
                <select name="id_almacen" class="form-control" required>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" {{ $entrada->id_almacen == $almacen->id ? 'selected' : '' }}>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <h5 class="mt-4 mb-3">Productos e Insumos</h5>
            <div id="items-container">
                @foreach($entrada->detalles as $i => $detalle)
                    <div class="form-row align-items-end mb-2 detalle-row">
                        <div class="col-md-3">
                            <label>Tipo</label>
                            <select name="items[{{ $i }}][tipo]" class="form-control tipo-select">
                                <option value="producto" {{ $detalle->id_producto ? 'selected' : '' }}>Producto</option>
                                <option value="insumo" {{ $detalle->id_insumo ? 'selected' : '' }}>Insumo</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Producto e Insumo</label>
                            <select name="items[{{ $i }}][id]" class="form-control id-select">
                                @if($detalle->id_producto)
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" data-tipo="producto" {{ $detalle->id_producto == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                @elseif($detalle->id_insumo)
                                    @foreach($insumos as $insumo)
                                        <option value="{{ $insumo->id }}" data-tipo="insumo" {{ $detalle->id_insumo == $insumo->id ? 'selected' : '' }}>
                                            {{ $insumo->nombre_completo }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Cantidad</label>
                            <input type="number" name="items[{{ $i }}][cantidad]" step="0.01" class="form-control" value="{{ $detalle->cantidad }}" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-remove-detalle">Eliminar</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-success my-3" id="btn-add-detalle">+ Agregar Producto o Insumo</button>
            <br>
            <button type="submit" class="btn btn-primary">Actualizar Entrada</button>
            <a href="{{ route('admin.entradas.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

{{-- Template para nuevos detalles --}}
<div id="detalle-template" style="display:none;">
    <div class="form-row align-items-end mb-2 detalle-row">
        <div class="col-md-3">
            <label>Tipo</label>
            <select class="form-control tipo-select">
                <option value="producto">Producto</option>
                <option value="insumo">Insumo</option>
            </select>
        </div>
        <div class="col-md-5">
            <label>Producto e Insumo</label>
            <select class="form-control id-select">
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" data-tipo="producto">{{ $producto->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label>Cantidad</label>
            <input type="number" class="form-control cantidad-input" step="0.01" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove-detalle">Eliminar</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let productos = @json($productos);
    let insumos = @json($insumos);

    document.addEventListener('DOMContentLoaded', function () {
        let itemsContainer = document.getElementById('items-container');
        let btnAddDetalle = document.getElementById('btn-add-detalle');
        let detalleTemplate = document.getElementById('detalle-template').innerHTML;

        // Eliminar detalle
        itemsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-detalle')) {
                e.target.closest('.detalle-row').remove();
            }
        });

        // Agregar nuevo detalle
        btnAddDetalle.addEventListener('click', function () {
            let index = itemsContainer.querySelectorAll('.detalle-row').length;
            let temp = document.createElement('div');
            temp.innerHTML = detalleTemplate;
            let row = temp.firstElementChild;

            row.querySelector('.tipo-select').setAttribute('name', `items[${index}][tipo]`);
            row.querySelector('.id-select').setAttribute('name', `items[${index}][id]`);
            row.querySelector('.cantidad-input').setAttribute('name', `items[${index}][cantidad]`);

            itemsContainer.appendChild(row);
        });

        // Filtrar productos/insumos según el tipo seleccionado
        itemsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('tipo-select')) {
                let tipo = e.target.value;
                let row = e.target.closest('.detalle-row');
                let idSelect = row.querySelector('.id-select');
                let options = '';
                if (tipo === 'producto') {
                    productos.forEach(function(p) {
                        options += `<option value="${p.id}" data-tipo="producto">${p.nombre}</option>`;
                    });
                } else {
                    insumos.forEach(function(i) {
                        options += `<option value="${i.id}" data-tipo="insumo">${i.nombre_completo}</option>`;
                    });
                }
                idSelect.innerHTML = options;
            }
        });
    });
</script>
@endsection