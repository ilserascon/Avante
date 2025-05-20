@extends('layouts.stisla')

@section('title', 'Editar Tipo de Insumo')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Tipo de Insumo</h1>
    </div>

    <div class="section-body">
        <form method="POST" action="{{ route('admin.tipo-insumos.update', $tipoInsumo->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $tipoInsumo->nombre) }}" required>
            </div>
            <div class="form-group">
                <label for="campo1">Campo 1</label>
                <input type="text" id="campo1" name="campo1" class="form-control" value="{{ old('campo1', $tipoInsumo->campo1) }}">
            </div>
            <div class="form-group">
                <label for="campo2">Campo 2</label>
                <input type="text" id="campo2" name="campo2" class="form-control" value="{{ old('campo2', $tipoInsumo->campo2) }}">
            </div>
            <div class="form-group">
                <label for="campo3">Campo 3</label>
                <input type="text" id="campo3" name="campo3" class="form-control" value="{{ old('campo3', $tipoInsumo->campo3) }}">
            </div>
            <div class="form-group">
                <label for="campo4">Campo 4</label>
                <input type="text" id="campo4" name="campo4" class="form-control" value="{{ old('campo4', $tipoInsumo->campo4) }}">
            </div>
            <div class="form-group">
                <label for="campo5">Campo 5</label>
                <input type="text" id="campo5" name="campo5" class="form-control" value="{{ old('campo5', $tipoInsumo->campo5) }}">
            </div>
            <div class="form-group">
                <label for="campo6">Campo 6</label>
                <input type="text" id="campo6" name="campo6" class="form-control" value="{{ old('campo6', $tipoInsumo->campo6) }}">
            </div>
            <div class="form-group">
                <label for="campo7">Campo 7</label>
                <input type="text" id="campo7" name="campo7" class="form-control" value="{{ old('campo7', $tipoInsumo->campo7) }}">
            </div>
            <div class="form-group">
                <label for="campo8">Campo 8</label>
                <input type="text" id="campo8" name="campo8" class="form-control" value="{{ old('campo8', $tipoInsumo->campo8) }}">
            </div>
            <div class="form-group">
                <label for="campo9">Campo 9</label>
                <input type="text" id="campo9" name="campo9" class="form-control" value="{{ old('campo9', $tipoInsumo->campo9) }}">
            </div>
            <div class="form-group">
                <label for="campo10">Campo 10</label>
                <input type="text" id="campo10" name="campo10" class="form-control" value="{{ old('campo10', $tipoInsumo->campo10) }}">
            </div>
            <div class="form-group">
                <label for="campo11">Campo 11</label>
                <input type="text" id="campo11" name="campo11" class="form-control" value="{{ old('campo11', $tipoInsumo->campo11) }}">
            </div>
            <div class="form-group">
                <label for="campo12">Campo 12</label>
                <input type="text" id="campo12" name="campo12" class="form-control" value="{{ old('campo12', $tipoInsumo->campo12) }}">
            </div>
            <div class="form-group">
                <label for="campo13">Campo 13</label>
                <input type="text" id="campo13" name="campo13" class="form-control" value="{{ old('campo13', $tipoInsumo->campo13) }}">
            </div>
            <div class="form-group">
                <label for="campo14">Campo 14</label>
                <input type="text" id="campo14" name="campo14" class="form-control" value="{{ old('campo14', $tipoInsumo->campo14) }}">
            </div>
            <div class="form-group">
                <label for="campo15">Campo 15</label>
                <input type="text" id="campo15" name="campo15" class="form-control" value="{{ old('campo15', $tipoInsumo->campo15) }}">
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('admin.tipo-insumos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection