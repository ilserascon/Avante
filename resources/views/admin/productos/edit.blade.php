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
                                    <div class="form-row align-items-end mb-2 insumo-item">
                                        <div class="col-md-6">
                                            <label>Insumo</label>
                                            <select name="insumos[{{$index}}][id]" class="form-control insumo-select">
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
                                            <input type="number" name="insumos[{{ $index }}][cantidad]" class="form-control" min="0" step="0.01" value="{{ $insumo->pivot->cantidad }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-block remove-insumo">Eliminar</button>
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

{{-- Genera las opciones de insumos para JS --}}
<script>
    const insumoOptions = `
        @foreach($insumos as $insumo)
            <option value="{{ $insumo->id }}">{{ $insumo->nombre_completo }}</option>
        @endforeach
    `;
</script>

<script>
    document.getElementById('add-insumo').addEventListener('click', function () {
        const container = document.getElementById('insumos-container');
        const insumoIndex = container.querySelectorAll('.insumo-item').length;

        const insumoDiv = document.createElement('div');
        insumoDiv.classList.add('form-row', 'align-items-end', 'mb-2', 'insumo-item');
        insumoDiv.innerHTML = `
            <div class="col-md-6">
                <label>Insumo</label>
                <select name="insumos[${insumoIndex}][id]" class="form-control insumo-select" required>
                    <option value="">Seleccione un insumo</option>
                    ${insumoOptions}
                </select>
            </div>
            <div class="col-md-4">
                <label>Cantidad</label>
                <input type="number" name="insumos[${insumoIndex}][cantidad]" class="form-control" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-block remove-insumo">Eliminar</button>
            </div>
        `;

        container.appendChild(insumoDiv);

        insumoDiv.querySelector('.insumo-select').addEventListener('change', function () {
            const selectedValue = this.value;
            const allSelects = document.querySelectorAll('.insumo-select');
            let duplicate = false;

            allSelects.forEach(select => {
                if (select !== this && select.value === selectedValue) {
                    duplicate = true;
                }
            });

            if (duplicate) {
                alert('No puedes repetir el mismo insumo');
                this.value = '';
            }
        });

        insumoDiv.querySelector('.remove-insumo').addEventListener('click', function () {
            insumoDiv.remove();
        });
    });

    document.querySelectorAll('.remove-insumo').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('.insumo-item').remove();
        });
    });
</script>
@endsection