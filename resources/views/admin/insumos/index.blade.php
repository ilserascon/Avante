@extends('layouts.stisla')

@section('title', 'Insumos')

@section('content')

<div class="section">
    <div class="section-header">
        <h1>Insumos</h1>
        <div class="section-header-button ml-auto">
            <a href="{{ route('admin.insumos.create') }}" class="btn btn-primary">Nuevo Insumo</a>
            <button class="btn btn-success ml-2" data-toggle="modal" data-target="#importModal">
                Importar Insumos
            </button>
        </div>
    </div>

    <div class="section-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.insumos.index') }}" class="mb-4">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col">
                    <select name="tipo_insumo" class="form-control">
                        <option value="">Seleccionar Tipo de Insumo</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ request('tipo_insumo') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="estado" class="form-control">
                        <option value="habilitado" {{ $estado == 'habilitado' ? 'selected' : '' }}>Habilitados</option>
                        <option value="inhabilitado" {{ $estado == 'inhabilitado' ? 'selected' : '' }}>Inhabilitados</option>
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="{{ route('admin.insumos.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header">
                <h4>Lista de Insumos</h4>
            </div>
            <div class="card-body table-responsive">

            @if(!$tipoSeleccionado)
                <div class="text-center p-4">
                    <h5>Seleccione un tipo de insumo para ver sus campos.</h5>
                </div>
            @elseif($insumos->isEmpty())
                <div class="text-center p-4">
                    <h5>No hay insumos registrados para este tipo.</h5>
                </div>
            @else
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Proveedor</th>
                            <th>Costo</th>
                            <th>Precio Público</th>
                            <th>Utilidad</th>
                            @foreach($camposDinamicos as $campo => $valor)
                                <th>{{ $valor }}</th>
                            @endforeach
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($insumos as $insumo)
                            <tr>
                                <td>{{ $insumo->nombre }}</td>
                                <td>{{ $insumo->proveedor->nombre ?? 'N/A' }}</td>
                                <td>{{ $insumo->costo }}</td>
                                <td>{{ $insumo->precio_publico }}</td>
                                <td>{{ $insumo->utilidad }}</td>
                                @foreach($camposDinamicos as $campo => $valor)
                                <td>{{ $insumo->$campo }}</td>
                                @endforeach
                                <td>
                                    @if($insumo->borrado == 0)
                                    <a href="{{ route('admin.insumos.edit', $insumo->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.insumos.destroy', $insumo->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Inhabilitar insumo?')">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.insumos.habilitar', $insumo->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm" onclick="return confirm('¿Habilitar insumo?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $insumos->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @endif

            </div>
        </div>
    </div>
</div>
    <!-- Modal de Importación -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.insumos.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Importar Insumos desde Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="tipoInsumo">Tipo de Insumo</label>
                        <select name="id_tipo_insumo" class="form-control" required>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="archivo">Archivo Excel</label>
                        <input type="file" name="archivo" class="form-control-file" required accept=".xlsx,.xls,.csv">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>

@endsection
