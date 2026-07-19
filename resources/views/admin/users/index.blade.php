@extends('layouts.stisla')

@section('title', 'Usuarios')

@section('content')
@include('admin.partials.professional-styles')

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Usuarios</h3>
                        <p class="hero-subtitle">Gestione las cuentas y roles del sistema.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="field-label">Nombre</label>
                            <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="Buscar por nombre">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="field-label">Rol</label>
                            <select name="role_id" class="form-control">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="field-label">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="habilitado" {{ (isset($estado) && $estado == 'habilitado') ? 'selected' : '' }}>Habilitados</option>
                                <option value="inhabilitado" {{ (isset($estado) && $estado == 'inhabilitado') ? 'selected' : '' }}>Inhabilitados</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Filtrar</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="section-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card table-card">
                <div class="card-body table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Creado</th>
                                <th>Estado</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="record-link">
                                            {{ $user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role->nombre ?? '-' }}</td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($user->borrado == 0)
                                            <span class="status-chip status-active">Activo</span>
                                        @else
                                            <span class="status-chip status-inactive">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="actions-wrap">
                                            @if($user->borrado == 0)
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn btn-edit" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="mb-0 d-inline js-registro-estado-form" data-accion="inhabilitar" data-entidad="usuario">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn btn-delete" title="Inhabilitar">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.habilitar', $user->id) }}" method="POST" class="mb-0 d-inline js-registro-estado-form" data-accion="habilitar" data-entidad="usuario">
                                                    @csrf
                                                    <button type="submit" class="action-btn btn-enable" title="Habilitar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert@2.1.2/dist/sweetalert.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-registro-estado-form').forEach(function (form) {
        var enviando = false;

        form.addEventListener('submit', function (event) {
            if (enviando) {
                return;
            }

            event.preventDefault();

            var esInhabilitar = form.getAttribute('data-accion') === 'inhabilitar';
            var entidad = form.getAttribute('data-entidad') || 'registro';

            swal({
                title: '¿Está seguro?',
                text: esInhabilitar
                    ? '¿Desea inhabilitar este ' + entidad + '?'
                    : '¿Desea habilitar este ' + entidad + '?',
                icon: 'warning',
                buttons: ['Cancelar', esInhabilitar ? 'Sí, inhabilitar' : 'Sí, habilitar'],
                dangerMode: esInhabilitar,
            }).then(function (confirmado) {
                if (confirmado) {
                    enviando = true;
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
