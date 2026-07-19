@extends('layouts.stisla')

@section('title', 'Nuevo Producto')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Nuevo Producto</h3>
                        <p class="hero-subtitle">Registre un nuevo producto en el catálogo.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.productos.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.productos.store') }}">
                @csrf

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información general</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_tipo_producto" class="field-label">Tipo de Producto</label>
                                    <select name="id_tipo_producto" id="id_tipo_producto" class="form-control @error('id_tipo_producto') is-invalid @enderror">
                                        <option value="">Seleccione un tipo</option>
                                        @foreach ($tiposProducto as $tipo)
                                            <option value="{{ $tipo->id }}" {{ old('id_tipo_producto') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_tipo_producto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombre" class="field-label">Nombre</label>
                                    <input name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-md-0">
                                    <label for="precio" class="field-label">Precio</label>
                                    <input type="number" name="precio" id="precio" class="form-control @error('precio') is-invalid @enderror" min="0" step="0.01" value="{{ old('precio') }}">
                                    @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-0">
                                    <label for="descripcion" class="field-label">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion') }}</textarea>
                                    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card form-card" id="insumos-section" style="display: none;">
                    <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <h5>Insumos del producto</h5>
                            <div class="text-muted">Asigne los insumos necesarios para productos tipo cortinero.</div>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-insumo">
                                <i class="fas fa-plus mr-1"></i> Agregar Insumo
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="insumos-container">
                            <div class="empty-state" id="empty-insumos-msg">
                                <i class="fas fa-tools fa-2x d-block"></i>
                                <p class="mb-0 small">No hay insumos agregados.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Guardar Producto
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const insumosOptions = `
        <option value="">Seleccione un insumo</option>
        @foreach ($insumos as $insumo)
            <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
        @endforeach
    `;

    const insumosContainer = document.getElementById('insumos-container');
    const emptyInsumosMsg = document.getElementById('empty-insumos-msg');
    const insumosSection = document.getElementById('insumos-section');
    const tipoProductoSelect = document.getElementById('id_tipo_producto');

    function initializeSelect2(element) {
        $(element).select2({
            placeholder: 'Seleccione un insumo',
            width: '100%',
            allowClear: true,
            dropdownParent: $(element).parent()
        });
    }

    function toggleEmptyInsumos() {
        const hasItems = insumosContainer.querySelectorAll('.item-row').length > 0;
        if (emptyInsumosMsg) {
            emptyInsumosMsg.style.display = hasItems ? 'none' : 'block';
        }
    }

    function toggleInsumosSection() {
        const selectedValue = tipoProductoSelect.value;
        const isCortinero = selectedValue === '1' || (tipoProductoSelect.options[tipoProductoSelect.selectedIndex]?.text || '').toLowerCase() === 'cortinero';
        insumosSection.style.display = isCortinero ? 'block' : 'none';
    }

    tipoProductoSelect.addEventListener('change', toggleInsumosSection);
    toggleInsumosSection();

    document.getElementById('add-insumo').addEventListener('click', function () {
        const insumoIndex = insumosContainer.querySelectorAll('.item-row').length;
        const insumoDiv = document.createElement('div');
        insumoDiv.classList.add('item-row');

        insumoDiv.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="status-chip status-active" style="text-transform:none;">Insumo ${insumoIndex + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-insumo" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="form-row align-items-end">
                <div class="col-md-7 mb-2 mb-md-0">
                    <label class="field-label">Insumo</label>
                    <select name="insumos[${insumoIndex}][id]" class="form-control insumo-select" required>
                        ${insumosOptions}
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="field-label">Cantidad</label>
                    <input type="number" name="insumos[${insumoIndex}][cantidad]" class="form-control" min="0" step="0.01" placeholder="Cantidad" required>
                </div>
            </div>
        `;

        insumosContainer.appendChild(insumoDiv);
        initializeSelect2(insumoDiv.querySelector('select'));
        toggleEmptyInsumos();
    });

    insumosContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-insumo');
        if (btn) {
            const row = btn.closest('.item-row');
            const select = row.querySelector('.insumo-select');
            if (select && $(select).hasClass('select2-hidden-accessible')) {
                $(select).select2('destroy');
            }
            row.remove();
            toggleEmptyInsumos();
        }
    });
</script>
@endsection
