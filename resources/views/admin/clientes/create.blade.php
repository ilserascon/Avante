@extends('layouts.stisla')

@section('title', 'Nuevo Cliente')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Nuevo Cliente</h3>
                        <p class="hero-subtitle">Registre un nuevo cliente en el sistema.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.clientes.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.clientes.store') }}">
                @csrf

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información general</h5>&nbsp;&nbsp;
                        <div class="text-muted">Datos principales del cliente.</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre" class="field-label">Nombre</label>
                            <input name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="rfc" class="field-label">RFC</label>
                                <input name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc') }}">
                                @error('rfc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="razon_social" class="field-label">Razón Social</label>
                                <input name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social') }}">
                                @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telefono" class="field-label">Teléfono</label>
                                <input name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="email" class="field-label">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Dirección</h5>&nbsp;&nbsp;
                        <div class="text-muted">Ubicación y código postal del cliente.</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-9 mb-md-0">
                                <label for="direccion" class="field-label">Dirección</label>
                                <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group col-md-3 mb-0">
                                <label for="codigo_postal" class="field-label">Código Postal</label>
                                <input type="text" name="codigo_postal" id="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal') }}">
                                @error('codigo_postal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Guardar Cliente
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
