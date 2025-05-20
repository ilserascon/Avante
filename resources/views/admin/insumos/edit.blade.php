@extends('layouts.stisla')

@section('title', 'Editar Insumo')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Insumo</h1>
    </div>

    <div class="section-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.insumos.update', $insumo->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $insumo->nombre) }}">
            </div>

            <div class="form-group">
                <label>Proveedor</label>
                <select name="id_proveedor" class="form-control">
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ $insumo->id_proveedor == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Tipo de Insumo</label>
                <select name="id_tipo_insumo" class="form-control" readonly disabled>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ $insumo->id_tipo_insumo == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                <!-- Este hidden es importante para que se mande el valor -->
                <input type="hidden" name="id_tipo_insumo" value="{{ $insumo->id_tipo_insumo }}">
            </div>

            <div class="form-group">
                <label>Costo</label>
                <input type="number" name="costo" class="form-control" value="{{ old('costo', $insumo->costo) }}">
            </div>

            <div class="form-group">
                <label>Precio Público</label>
                <input type="number" name="precio_publico" class="form-control" value="{{ old('precio_publico', $insumo->precio_publico) }}">
            </div>

            <div class="form-group">
                <label>Utilidad</label>
                <input type="number" name="utilidad" class="form-control" value="{{ old('utilidad', $insumo->utilidad) }}">
            </div>

            @foreach($camposDinamicos as $campoNombre => $campoLabel)
                <div class="form-group">
                    <label>{{ $campoLabel }}</label>
                    <input type="text" name="{{ $campoNombre }}" class="form-control" value="{{ old($campoNombre, $insumo->$campoNombre) }}">
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="{{ route('admin.insumos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection