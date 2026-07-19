@extends('layouts.stisla')

@section('title', 'Nueva Entrada')

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

    .entrada-form .add-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .entrada-form .add-buttons .btn {
        border-radius: 10px;
    }

    .entrada-form .actions-bar .btn {
        border-radius: 10px;
    }

    .entrada-form .empty-items {
        text-align: center;
        color: #6b7b95;
        padding: 2rem 1rem;
        border: 2px dashed #e8eef8;
        border-radius: 12px;
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
                        <h3>Nueva Entrada</h3>
                        <p class="text-muted mb-0 small">Registre productos o insumos al almacén seleccionado.</p>
                    </div>
                    <div class="hero-actions">
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

            <form method="POST" action="{{ route('admin.entradas.store') }}">
                @csrf

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Seleccione el almacén destino de la entrada.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="id_almacen" class="field-label">Almacén</label>
                                <select name="id_almacen" id="id_almacen" class="form-control @error('id_almacen') is-invalid @enderror" required>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}" {{ old('id_almacen') == $almacen->id ? 'selected' : '' }}>
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
                            <div class="text-muted">Agregue los artículos que ingresarán al inventario.</div>
                        </div>
                        <div class="add-buttons mt-3 mt-md-0">
                            <button type="button" id="add-product" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-box mr-1"></i> Agregar Producto
                            </button>
                            <button type="button" id="add-insumo" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-tools mr-1"></i> Agregar Insumo
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="items-container">
                            <div class="empty-items" id="empty-items-msg">
                                <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:0.4;"></i>
                                No hay artículos agregados. Use los botones de arriba para comenzar.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Guardar Entrada
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.entradas.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let index = 0;
const itemsContainer = document.getElementById('items-container');
const emptyItemsMsg = document.getElementById('empty-items-msg');

function toggleEmptyMessage() {
    const hasItems = itemsContainer.querySelectorAll('.item-row').length > 0;
    if (emptyItemsMsg) {
        emptyItemsMsg.style.display = hasItems ? 'none' : 'block';
    }
}

function buildItemRow(type) {
    const isProduct = type === 'product';
    const badgeClass = isProduct ? 'type-producto' : 'type-insumo';
    const badgeLabel = isProduct ? 'Producto' : 'Insumo';
    const selectName = isProduct ? `items[${index}][id_producto]` : `items[${index}][id_insumo]`;
    const options = isProduct
        ? `@foreach($productos as $producto)<option value="{{ $producto->id }}">{{ $producto->nombre }}</option>@endforeach`
        : `@foreach($insumos as $insumo)<option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>@endforeach`;
    const placeholder = isProduct ? 'Seleccione un producto...' : 'Seleccione un insumo...';

    return `
        <div class="item-row">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="type-badge ${badgeClass}" style="display:inline-flex;align-items:center;padding:0.28rem 0.65rem;border-radius:999px;font-size:0.75rem;font-weight:700;letter-spacing:0.2px;text-transform:uppercase;background:${isProduct ? '#e7f1ff' : '#fff5df'};color:${isProduct ? '#1d4ed8' : '#b45309'};">${badgeLabel}</span>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="form-row align-items-end">
                <div class="col-md-8 mb-2 mb-md-0">
                    <label class="field-label">${badgeLabel}</label>
                    <select name="${selectName}" class="form-control" required>
                        <option value="">${placeholder}</option>
                        ${options}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="field-label">Cantidad</label>
                    <input type="number" name="items[${index}][cantidad]" class="form-control" placeholder="Cantidad" min="1" step="0.01" required>
                </div>
            </div>
        </div>`;
}

document.getElementById('add-product').onclick = function () {
    itemsContainer.insertAdjacentHTML('beforeend', buildItemRow('product'));
    index++;
    toggleEmptyMessage();
};

document.getElementById('add-insumo').onclick = function () {
    itemsContainer.insertAdjacentHTML('beforeend', buildItemRow('insumo'));
    index++;
    toggleEmptyMessage();
};

itemsContainer.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-remove-item');
    if (btn) {
        btn.closest('.item-row').remove();
        toggleEmptyMessage();
    }
});
</script>
@endsection
