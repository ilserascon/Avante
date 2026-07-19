@extends('layouts.stisla')

@section('title', 'Crear Tipo de Insumo')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Crear Tipo de Insumo</h3>
                        <p class="hero-subtitle">Defina el nombre y hasta 15 campos personalizados.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.tipo-insumos.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.tipo-insumos.store') }}">
                @csrf

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Nombre del tipo de insumo.</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label for="nombre" class="field-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                        </div>
                    </div>
                </div>

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Campos personalizados</h5>&nbsp;&nbsp;
                        <div class="text-muted">Etiquetas para los campos adicionales que tendrán los insumos de este tipo.</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            @for($i = 1; $i <= 15; $i++)
                                <div class="form-group col-md-4">
                                    <label for="campo{{ $i }}" class="field-label">Campo {{ $i }}</label>
                                    <input type="text" id="campo{{ $i }}" name="campo{{ $i }}" class="form-control" value="{{ old('campo'.$i) }}">
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.tipo-insumos.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
