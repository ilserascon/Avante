@extends('layouts.stisla')

@section('title', 'Nuevo Almacén')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Nuevo Almacén</h3>
                        <p class="hero-subtitle">Registre un almacén para gestionar existencias e inventario.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.almacenes.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.almacenes.store') }}">
                @csrf

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información del almacén</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nombre" class="field-label">Nombre</label>
                                <input name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                       value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6 mb-md-0">
                                <label for="ubicacion" class="field-label">Ubicación</label>
                                <input name="ubicacion" id="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror"
                                       value="{{ old('ubicacion') }}" required>
                                @error('ubicacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Guardar Almacén
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.almacenes.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
