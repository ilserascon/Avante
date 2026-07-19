@extends('layouts.stisla')

@section('title', 'Editar Usuario')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Editar Usuario - {{ $user->name }}</h3>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4">
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

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="card form-card">
                    <div class="card-header">
                        <h5>Información de la cuenta</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="field-label">Nombre</label>
                            <input name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="field-label">Correo electrónico</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="field-label">Nueva contraseña</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="form-text text-muted">Opcional. Déjela vacía si no desea cambiarla.</small>
                        </div>

                        <div class="form-group mb-0">
                            <label for="role_id" class="field-label">Rol</label>
                            <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ (int) old('role_id', $user->role_id) === (int) $role->id ? 'selected' : '' }}>
                                        {{ $role->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="actions-bar d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save mr-1"></i> Actualizar Usuario
                    </button>&nbsp;&nbsp;
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light border px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
