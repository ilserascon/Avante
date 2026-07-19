@extends('layouts.stisla')

@section('title', 'Editar Entrada')

@section('content')
<style>
    .entrada-form .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .entrada-form .hero-card h3 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #26344d;
    }

    .entrada-form .form-card,
    .entrada-form .items-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
        margin-bottom: 1.25rem;
    }

    .entrada-form .form-card .card-header,
    .entrada-form .items-card .card-header {
        background: #f5f8ff;
        border-bottom: 1px solid #e8eef8;
        padding: 0.9rem 1.25rem;
    }

    .entrada-form .form-card .card-header h5,
    .entrada-form .items-card .card-header h5 {
        margin: 0;
        color: #26344d;
        font-weight: 700;
    }

    .entrada-form .form-card .card-header .text-muted,
    .entrada-form .items-card .card-header .text-muted {
        font-size: 0.82rem;
    }

    .entrada-form .field-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6b7b95;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .entrada-form .item-row {
        background: #fff;
        border: 1px solid #e8eef8;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }

    .entrada-form .item-row:last-child {
        margin-bottom: 0;
    }

    .entrada-form .add-buttons .btn,
    .entrada-form .actions-bar .btn {
        border-radius: 10px;
    }

    .entrada-form .type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .entrada-form .type-producto {
        background: #e7f1ff;
        color: #1d4ed8;
    }

    .entrada-form .type-insumo {
        background: #fff5df;
        color: #b45309;
    }

    @media (max-width: 767.98px) {
        .entrada-form .hero-actions {
            margin-top: 0.75rem;
            width: 100%;
        }

        .entrada-form .hero-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="section">
    <div class="entrada-form">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Editar Entrada #{{ str_pad($entrada->id, 5, '0', STR_PAD_LEFT) }}</h3>
                        <p class="text-muted mb-0 small">Modifique el almacén o los artículos de esta entrada.</p>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.entradas.show', $entrada->id) }}" class="btn btn-light border px-4">
                            <i class="fas fa-eye mr-1"></i> Ver detalle
                        </a> &nbsp;
                        <a href="{{ route('admin.entradas.index') }}" class="btn btn-light border px-4">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.entradas.update', $entrada->id) }}">
                @csrf
                @method('PUT')

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Almacén destino de la entrada.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="id_almacen" class="field-label">Almacén</label>
                                <select name="id_almacen" id="id_almacen" class="form-control @error('id_almacen') is-invalid @enderror" required>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}" {{ (int) old('id_almacen', $entrada->id_almacen) === (int) $almacen->id ? 'selected' : '' }}>
                                            {{ $almacen->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_almacen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card items-card">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <h5>Productos e insumos</h5>
                            <div class="text-muted">Edite, agregue o elimine los artículos de la entrada.</div>
                        </div>
                        <div class="add-buttons mt-3 mt-md-0">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-detalle">
                                <i class="fas fa-plus mr-1"></i> Agregar artículo
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="items-container">
                            @foreach($entrada->detalles as $i => $detalle)
                                @php
                                    $esProducto = (bool) $detalle->id_producto;
                                @endphp
                                <div class="item-row detalle-row">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="type-badge badge-label {{ $esProducto ? 'type-producto' : 'type-insumo' }}">
                                            {{ $esProducto ? 'Producto' : 'Insumo' }}
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-detalle" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="form-row align-items-end">
                                        <div class="col-md-3 mb-2 mb-md-0">
                                            <label class="field-label">Tipo</label>
                                            <select name="items[{{ $i }}][tipo]" class="form-control tipo-select">
                                                <option value="producto" {{ $esProducto ? 'selected' : '' }}>Producto</option>
                                                <option value="insumo" {{ !$esProducto ? 'selected' : '' }}>Insumo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-5 mb-2 mb-md-0">
                                            <label class="field-label">Artículo</label>
                                            <select name="items[{{ $i }}][id]" class="form-control id-select">
                                                @if($esProducto)
                                                    @foreach($productos as $producto)
                                                        <option value="{{ $producto->id }}" data-tipo="producto" {{ (int) $detalle->id_producto === (int) $producto->id ? 'selected' : '' }}>
                                                            {{ $producto->nombre }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach($insumos as $insumo)
                                                        <option value="{{ $insumo->id }}" data-tipo="insumo" {{ (int) $detalle->id_insumo === (int) $insumo->id ? 'selected' : '' }}>
                                                            {{ $insumo->nombre_completo }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="field-label">Cantidad</label>
                                            <input type="number" name="items[{{ $i }}][cantidad]" step="0.01" min="1" class="form-control" value="{{ old('items.'.$i.'.cantidad', $detalle->cantidad) }}" required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Actualizar Entrada
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.entradas.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="detalle-template" style="display:none;">
    <div class="item-row detalle-row">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="type-badge type-producto badge-label">Producto</span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-detalle" title="Eliminar">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="form-row align-items-end">
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="field-label">Tipo</label>
                <select class="form-control tipo-select">
                    <option value="producto">Producto</option>
                    <option value="insumo">Insumo</option>
                </select>
            </div>
            <div class="col-md-5 mb-2 mb-md-0">
                <label class="field-label">Artículo</label>
                <select class="form-control id-select">
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" data-tipo="producto">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="field-label">Cantidad</label>
                <input type="number" class="form-control cantidad-input" step="0.01" min="1" required>
            </div>
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

        function updateBadge(row, tipo) {
            const badge = row.querySelector('.badge-label');
            if (!badge) return;
            badge.textContent = tipo === 'producto' ? 'Producto' : 'Insumo';
            badge.classList.toggle('type-producto', tipo === 'producto');
            badge.classList.toggle('type-insumo', tipo === 'insumo');
        }

        itemsContainer.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-detalle')) {
                e.target.closest('.detalle-row').remove();
            }
        });

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

        itemsContainer.addEventListener('change', function (e) {
            if (e.target.classList.contains('tipo-select')) {
                let tipo = e.target.value;
                let row = e.target.closest('.detalle-row');
                let idSelect = row.querySelector('.id-select');
                let options = '';

                if (tipo === 'producto') {
                    productos.forEach(function (p) {
                        options += `<option value="${p.id}" data-tipo="producto">${p.nombre}</option>`;
                    });
                } else {
                    insumos.forEach(function (i) {
                        options += `<option value="${i.id}" data-tipo="insumo">${i.nombre_completo}</option>`;
                    });
                }

                idSelect.innerHTML = options;
                updateBadge(row, tipo);
            }
        });
    });
</script>
@endsection
