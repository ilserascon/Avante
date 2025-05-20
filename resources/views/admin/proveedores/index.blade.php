@extends('layouts.stisla')

@section('title', 'Proveedores')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Proveedores</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.proveedores.create') }}" class="btn btn-primary">Nuevo Proveedor</a>
        </div>
    </div>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filtros --}}
        <form method="GET" action="{{ route('admin.proveedores.index') }}" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col">
                    <input type="text" name="rfc" value="{{ request('rfc') }}" class="form-control" placeholder="Buscar por RFC">
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="{{ route('admin.proveedores.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h4>Lista de Proveedores</h4>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Razón Social</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($proveedores as $proveedor)
                            <tr>
                                <td>{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->rfc }}</td>
                                <td>{{ $proveedor->razon_social }}</td>
                                <td>{{ $proveedor->telefono ?? '-' }}</td>
                                <td>{{ $proveedor->email ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.proveedores.edit', $proveedor->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.proveedores.destroy', $proveedor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar proveedor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $proveedores->appends(request()->query())->links('pagination::bootstrap-4') }}                
            </div>
    </div>
</div>
@endsection
