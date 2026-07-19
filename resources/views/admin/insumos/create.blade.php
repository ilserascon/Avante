@extends('layouts.stisla')

@section('title', 'Crear Insumo')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Crear Insumo</h3>
                        <p class="hero-subtitle">Registre un nuevo insumo en el catálogo.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.insumos.index') }}" class="btn btn-light border px-4">
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

            <form action="{{ route('admin.insumos.store') }}" method="POST">
                @csrf

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información general</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="nombre" class="field-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="id_proveedor" class="field-label">Proveedor</label>
                                <select name="id_proveedor" id="id_proveedor" class="form-control">
                                    <option value="">Seleccione...</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}" {{ old('id_proveedor') == $proveedor->id ? 'selected' : '' }}>
                                            {{ $proveedor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="id_tipo_insumo" class="field-label">Tipo de Insumo</label>
                                <select name="id_tipo_insumo" id="id_tipo_insumo" class="form-control" required>
                                    <option value="">Seleccione un tipo</option>
                                    @foreach($tiposInsumo as $tipo)
                                        <option value="{{ $tipo->id }}" data-campos='@json($tipo->campos_data)' {{ old('id_tipo_insumo') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Precios</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="costo" class="field-label">Costo</label>
                                <input type="number" name="costo" id="costo" class="form-control" step="0.01" value="{{ old('costo') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="precio_publico" class="field-label">Precio Público</label>
                                <input type="number" name="precio_publico" id="precio_publico" class="form-control" step="0.01" value="{{ old('precio_publico') }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="utilidad" class="field-label">Utilidad</label>
                                <input type="number" name="utilidad" id="utilidad" class="form-control" step="0.01" value="{{ old('utilidad') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card form-card" id="campos-dinamicos-card" style="display:none;">
                    <div class="card-header">
                        <h5>Campos adicionales</h5> &nbsp;&nbsp;
                        <div class="text-muted">Campos definidos por el tipo de insumo seleccionado.</div>
                    </div>
                    <div class="card-body">
                        <div id="campos-dinamicos"></div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Crear Insumo
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.insumos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('id_tipo_insumo').addEventListener('change', function() {
    const camposDiv = document.getElementById('campos-dinamicos');
    const camposCard = document.getElementById('campos-dinamicos-card');
    camposDiv.innerHTML = '';
    const selectedOption = this.options[this.selectedIndex];
    let campos = {};

    try {
        campos = JSON.parse(selectedOption.getAttribute('data-campos') || '{}');
    } catch (e) {}

    if (Object.keys(campos).length > 0) {
        camposCard.style.display = 'block';
        let fields = [];
        let count = 0;

        for (let key in campos) {
            fields.push(
                `<div class="form-group col-md-4">
                    <label class="field-label">${campos[key]}</label>
                    <input type="text" name="${key}" class="form-control">
                </div>`
            );
            count++;
            if (count % 3 === 0 || count === Object.keys(campos).length) {
                camposDiv.innerHTML += `<div class="form-row">${fields.join('')}</div>`;
                fields = [];
            }
        }
    } else {
        camposCard.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const tipoSelect = document.getElementById('id_tipo_insumo');
    if (tipoSelect.value) {
        tipoSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
