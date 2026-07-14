@extends('layouts.stisla')

@section('title', 'Editar Tipo de Producto')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Tipo de Producto</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tipo-productos.update', $tipoProducto->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $tipoProducto->nombre) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $tipoProducto->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Actualizar Tipo de Producto</button>
                    <a href="{{ route('admin.tipo-productos.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
