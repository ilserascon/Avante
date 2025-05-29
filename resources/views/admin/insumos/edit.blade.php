@extends('layouts.stisla')

@section('content')
    <form action="{{ route('admin.insumos.update', $insumo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $insumo->nombre) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="id_proveedor">Proveedor</label>
            <select name="id_proveedor" id="id_proveedor" class="form-control">
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}" {{ $insumo->id_proveedor == $proveedor->id ? 'selected' : '' }}>
                        {{ $proveedor->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="id_tipo_insumo">Tipo de Insumo</label>
            <select name="id_tipo_insumo" id="id_tipo_insumo" class="form-control" required>
                <option value="">Seleccione un tipo</option>
                @foreach($tiposInsumo as $tipo)
                    <option value="{{ $tipo->id }}" 
                        data-campos='@json($tipo->campos_data)' 
                        {{ $insumo->id_tipo_insumo == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="costo">Costo</label>
            <input type="number" name="costo" id="costo" value="{{ old('costo', $insumo->costo) }}" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="precio_publico">Precio Público</label>
            <input type="number" name="precio_publico" id="precio_publico" value="{{ old('precio_publico', $insumo->precio_publico) }}" class="form-control" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="utilidad">Utilidad</label>
            <input type="number" name="utilidad" id="utilidad" value="{{ old('utilidad', $insumo->utilidad) }}" class="form-control" step="0.01" required>
        </div>

        <div id="campos-dinamicos"></div>

        <button type="submit" class="btn btn-primary">Actualizar Insumo</button>
        <a href="{{ route('admin.insumos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>

    <script>
        const tipoSelect = document.getElementById('id_tipo_insumo');
        const camposDiv = document.getElementById('campos-dinamicos');

        const valoresActuales = @json($insumo->campos_extra ?? []);

        function actualizarCampos() {
            camposDiv.innerHTML = '';
            const selectedOption = tipoSelect.options[tipoSelect.selectedIndex];
            const campos = JSON.parse(selectedOption.getAttribute('data-campos') || '{}');

            for (let key in campos) {
                const valor = valoresActuales[key] || '';
                camposDiv.innerHTML += `
                    <div class="form-group">
                        <label for="${key}">${campos[key]}</label>
                        <input type="text" name="${key}" id="${key}" class="form-control" value="${valor}">
                    </div>
                `;
            }
        }

        tipoSelect.addEventListener('change', actualizarCampos);

        window.addEventListener('DOMContentLoaded', actualizarCampos);
    </script>
@endsection
