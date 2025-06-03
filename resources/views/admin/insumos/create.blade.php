@extends('layouts.stisla')

@section('title', 'Crear Insumo')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Crear Insumo</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Formulario de Creación</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.insumos.store') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="id_proveedor">Proveedor</label>
                            <select name="id_proveedor" id="id_proveedor" class="form-control">
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="id_tipo_insumo">Tipo de Insumo</label>
                            <select name="id_tipo_insumo" id="id_tipo_insumo" class="form-control" required>
                                <option value="">Seleccione un tipo</option>
                                @foreach($tiposInsumo as $tipo)
                                    <option value="{{ $tipo->id }}" data-campos='@json($tipo->campos_data)'>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="costo">Costo</label>
                            <input type="number" name="costo" id="costo" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="precio_publico">Precio Público</label>
                            <input type="number" name="precio_publico" id="precio_publico" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="utilidad">Utilidad</label>
                            <input type="number" name="utilidad" id="utilidad" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div id="campos-dinamicos"></div>

                    <button type="submit" class="btn btn-primary">Crear Insumo</button>
                    <a href="{{ route('admin.insumos.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('id_tipo_insumo').addEventListener('change', function() {
    const camposDiv = document.getElementById('campos-dinamicos');
    camposDiv.innerHTML = '';
    const selectedOption = this.options[this.selectedIndex];
    let campos = {};
    try {
        campos = JSON.parse(selectedOption.getAttribute('data-campos'));
    } catch(e) {}

    if (Object.keys(campos).length > 0) {
        let fields = [];
        let count = 0;
        for (let key in campos) {
            fields.push(
                `<div class="form-group col-md-4">
                    <label>${campos[key]}</label>
                    <input type="text" name="${key}" class="form-control">
                </div>`
            );
            count++;
            if (count % 3 === 0 || count === Object.keys(campos).length) {
                camposDiv.innerHTML += `<div class="form-row">${fields.join('')}</div>`;
                fields = [];
            }
        }
    }
});
</script>
@endsection