@extends('layouts.stisla')

@section('title', 'Editar Entrada')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Entrada</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.entradas.update', $entrada->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="id_almacen">Almacén</label>
                        <input name="id_almacen" class="form-control" value="{{ $entrada->id_almacen }}" required>
                    </div>

                    <div class="form-group">
                        <label for="id_usuario">Usuario</label>
                        <input name="id_usuario" class="form-control" value="{{ $entrada->id_usuario }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.entradas.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection