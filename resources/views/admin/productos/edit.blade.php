@extends('layouts.stisla')

@section('title', 'Editar Producto')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Producto</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.productos.update', $producto) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $producto->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <h5>Insumos</h5>
                        <button type="button" id="add-insumo" class="btn btn-sm btn-success mb-3">Agregar Insumo</button>
                        <div id="insumos-container">
                            @if ($producto->insumos && $producto->insumos->isNotEmpty())
                                @foreach ($producto->insumos as $index => $insumo)
                                <div class="row mb-3 insumo-row">
                                    <div class="col-md-6">
                                        <label>Insumo</label>
                                        <select name="insumos[{{ $index }}][id]" class="form-control insumo-select" required>
                                            <option value="">Seleccione un insumo</option>
                                            @foreach ($insumos as $opcion)
                                                <option value="{{ $opcion->id }}" {{ $insumo->id == $opcion->id ? 'selected' : '' }}>
                                                    {{ $opcion->nombre_completo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Cantidad</label>
                                        <input type="number" name="insumos[{{ $index }}][cantidad]" class="form-control" min="0" step="0.01" value="{{ $insumo->pivot->cantidad ?? '' }}" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-remove-insumo">Eliminar</button>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p>No hay insumos asociados a este producto.</p>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                    <a href="{{ route('admin.productos.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    {{-- Select2 Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('scripts')
    {{-- jQuery y Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- Opciones de insumos para clonado --}}
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

            // Inicializa Select2 en insumos existentes
            initSelect2($('#insumos-container'));

            $('#add-insumo').click(function () {
                const container = $('#insumos-container');
                const index = container.find('.insumo-row').length;

                const newRow = $(`
                    <div class="row mb-3 insumo-row">
                        <div class="col-md-6">
                            <label>Insumo</label>
                            <select name="insumos[${index}][id]" class="form-control insumo-select" required>
                                <option value="">Seleccione un insumo</option>
                                ${insumoOptions}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Cantidad</label>
                            <input type="number" name="insumos[${index}][cantidad]" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-insumo">Eliminar</button>
                        </div>
                    </div>
                `);

                container.append(newRow);
                initSelect2(newRow);
            });

            // Delegación para eventos de eliminación
            $(document).on('click', '.btn-remove-insumo', function () {
                const row = $(this).closest('.insumo-row');
                row.find('.insumo-select').select2('destroy');
                row.remove();
            });
        });
    </script>
@endsection
