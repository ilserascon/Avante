@extends('layouts.stisla')

@section('title', 'Editar Tipo de Producto')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Editar Tipo de Producto</h3>
                        <p class="hero-subtitle">{{ $tipoProducto->nombre }}</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.tipo-productos.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.tipo-productos.update', $tipoProducto->id) }}">
                @csrf
                @method('PUT')

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Nombre y descripción de la categoría.</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre" class="field-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $tipoProducto->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-0">
                            <label for="descripcion" class="field-label">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $tipoProducto->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Actualizar
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.tipo-productos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
