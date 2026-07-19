@extends('layouts.stisla')

@section('title', 'Editar Proveedor')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Editar Proveedor</h3>
                        <p class="hero-subtitle">{{ $proveedor->nombre }}</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.proveedores.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.proveedores.update', $proveedor->id) }}">
                @csrf
                @method('PUT')

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información del proveedor</h5>
                        <div class="text-muted">Datos fiscales y de contacto.</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre" class="field-label">Nombre</label>
                            <input name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $proveedor->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="rfc" class="field-label">RFC</label>
                                <input name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror"
                                       value="{{ old('rfc', $proveedor->rfc) }}" required>
                                @error('rfc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="razon_social" class="field-label">Razón Social</label>
                                <input name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
                                       value="{{ old('razon_social', $proveedor->razon_social) }}" required>
                                @error('razon_social')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telefono" class="field-label">Teléfono</label>
                                <input name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono', $proveedor->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="email" class="field-label">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $proveedor->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Estado</h5>
                        <div class="text-muted">Estado actual del proveedor en el sistema.</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="borrado" class="field-label">Estado</label>
                            <select name="borrado" id="borrado" class="form-control @error('borrado') is-invalid @enderror" required>
                                <option value="0" {{ (int) old('borrado', $proveedor->borrado) === 0 ? 'selected' : '' }}>Activo</option>
                                <option value="1" {{ (int) old('borrado', $proveedor->borrado) === 1 ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('borrado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Actualizar Proveedor
                    </button>
                    <a href="{{ route('admin.proveedores.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
