@extends('layouts.stisla')

@section('title', 'Usuarios')

@section('content')
<div class="section">
  <div class="section-header">
    <h1>Usuarios</h1>
    <div class="section-header-button ml-auto">
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Nuevo Usuario</a>
    </div>
  </div>

  <div class="section-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
      <div class="form-row">
        <div class="col">
          <select name="estado" class="form-control">
            <option value="habilitado" {{ (isset($estado) && $estado == 'habilitado') ? 'selected' : '' }}>Habilitados</option>
            <option value="inhabilitado" {{ (isset($estado) && $estado == 'inhabilitado') ? 'selected' : '' }}>Inhabilitados</option>
          </select>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-primary">Filtrar</button>
          <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Limpiar</a>
        </div>
      </div>
    </form>

    <div class="card">
      <div class="card-header">
        <h4>Lista de Usuarios</h4>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Email</th>
              <th>Creado</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                <td>{{ $user->role->nombre ?? '-' }}</td>
                <td>
                  @if($user->borrado == 0)
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button class="btn btn-danger btn-sm" onclick="return confirm('¿Inhabilitar usuario?')"><i class="fas fa-ban"></i></button>
                    </form>
                  @else
                    <form action="{{ route('admin.users.habilitar', $user->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-success btn-sm" onclick="return confirm('¿Habilitar usuario?')"><i class="fas fa-check"></i></button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center">No hay usuarios registrados.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
