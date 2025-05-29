@extends('layouts.stisla')

@section('title', 'Nuevo Cliente')

@section('content')
<div class="section">
  <div class="section-header">
    <h1>Nuevo Cliente</h1>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.clientes.store') }}">
          @csrf

          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label for="rfc">RFC</label>
              <input name="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc') }}">
              @error('rfc') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group col-md-4">
              <label for="razon_social">Razón Social</label>
              <input name="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social') }}">
              @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group col-md-4">
              <label for="telefono">Teléfono</label>
              <input name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
              @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-row">
            <div class="form-group col-md-9">
              <label for="direccion">Dirección</label>
              <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
              @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="form-group col-md-3">
              <label for="codigo_postal">Código Postal</label>
              <input type="text" name="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal') }}">
              @error('codigo_postal') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Guardar</button>
          <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection