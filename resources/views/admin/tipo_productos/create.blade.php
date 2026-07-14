@extends('layouts.stisla')

@section('title', 'Nuevo Tipo de Producto')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Nuevo Tipo de Producto</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tipo-productos.store') }}">
                    @csrf

                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion') }}</textarea>
                        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('admin.tipo-productos.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
