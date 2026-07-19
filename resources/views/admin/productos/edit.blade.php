@extends('layouts.stisla')

@section('title', 'Editar Producto')

@section('content')
@include('admin.partials.professional-styles')

@php
    $esCortinero = $producto->id_tipo_producto == 1 || strtolower($producto->tipoProducto->nombre ?? '') === 'cortinero';
@endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h4>Editar - {{ $producto->nombre }}</h4>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-2">
                        @if ($esCortinero)
                            <a href="{{ route('admin.productos.insumos', $producto->id) }}" class="btn btn-light border px-4">
                                <i class="fas fa-list mr-1"></i> Ver insumos
                            </a> &nbsp;
                        @endif
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

            <form method="POST" action="{{ route('admin.productos.update', $producto) }}">
                @csrf
                @method('PUT')

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información general</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="id_tipo_producto" class="field-label">Tipo de Producto</label>
                                    <select id="id_tipo_producto" name="id_tipo_producto" class="form-control @error('id_tipo_producto') is-invalid @enderror">
                                        <option value="">Seleccione un tipo</option>
                                        @foreach ($tiposProducto as $tipo)
                                            <option value="{{ $tipo->id }}" {{ old('id_tipo_producto', $producto->id_tipo_producto) == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_tipo_producto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombre" class="field-label">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                           value="{{ old('nombre', $producto->nombre) }}" required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-md-0">
                                    <label for="precio" class="field-label">Precio</label>
                                    <input type="number" id="precio" name="precio" class="form-control @error('precio') is-invalid @enderror" min="0" step="0.01"
                                           value="{{ old('precio', $producto->precio) }}">
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-0">
                                    <label for="descripcion" class="field-label">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($esCortinero)
                    <div class="card form-card">
                        <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div>
                                <h5>Insumos del producto</h5>
                                <div class="text-muted">Asigne los insumos necesarios para este cortinero.</div>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <button type="button" id="add-insumo" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus mr-1"></i> Agregar Insumo
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="insumos-container">
                                @if ($producto->insumos && $producto->insumos->isNotEmpty())
                                    @foreach ($producto->insumos as $index => $insumo)
                                        <div class="item-row insumo-row">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="status-chip status-active" style="text-transform:none;">Insumo {{ $index + 1 }}</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-insumo" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="form-row align-items-end">
                                                <div class="col-md-7 mb-2 mb-md-0">
                                                    <label class="field-label">Insumo</label>
                                                    <select name="insumos[{{ $index }}][id]" class="form-control insumo-select" required>
                                                        <option value="">Seleccione un insumo</option>
                                                        @foreach ($insumos as $opcion)
                                                            <option value="{{ $opcion->id }}" {{ $insumo->id == $opcion->id ? 'selected' : '' }}>
                                                                {{ $opcion->nombre_completo }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="field-label">Cantidad</label>
                                                    <input type="number" name="insumos[{{ $index }}][cantidad]" class="form-control" min="0" step="0.01" value="{{ $insumo->pivot->cantidad ?? '' }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-tools fa-2x d-block"></i>
                                        <p class="mb-0 small">No hay insumos asociados a este producto.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Actualizar Producto
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const insumoOptions = `
        @foreach($insumos as $insumo)
            <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
        @endforeach
    `;
</script>
<script>
    $(document).ready(function () {
        function initSelect2(container) {
            container.find('.insumo-select').select2({
                placeholder: 'Seleccione un insumo',
                width: '100%',
                allowClear: true
            });
        }

        initSelect2($('#insumos-container'));

        $('#add-insumo').click(function () {
            const container = $('#insumos-container');
            container.find('.empty-state').remove();
            const index = container.find('.insumo-row').length;

            const newRow = $(`
                <div class="item-row insumo-row">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="status-chip status-active" style="text-transform:none;">Insumo ${index + 1}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-insumo" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="form-row align-items-end">
                        <div class="col-md-7 mb-2 mb-md-0">
                            <label class="field-label">Insumo</label>
                            <select name="insumos[${index}][id]" class="form-control insumo-select" required>
                                <option value="">Seleccione un insumo</option>
                                ${insumoOptions}
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="field-label">Cantidad</label>
                            <input type="number" name="insumos[${index}][cantidad]" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
            `);

            container.append(newRow);
            initSelect2(newRow);
        });

        $(document).on('click', '.btn-remove-insumo', function () {
            const row = $(this).closest('.insumo-row');
            row.find('.insumo-select').select2('destroy');
            row.remove();
        });
    });
</script>
@endsection
