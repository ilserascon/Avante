@extends('layouts.stisla')

@section('title', 'Productos')

@section('content')
@include('admin.partials.professional-styles')
@php $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false; @endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Productos</h3>
                        <p class="hero-subtitle">Gestione el catálogo de productos.</p>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.productos.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo Producto
                        </a>&nbsp;
                        <button class="btn btn-success px-4" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import mr-1"></i> Importar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.productos.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="field-label">Nombre o clave</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre o clave" value="{{ request('nombre') }}">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="field-label">Tipo de producto</label>
                            <select name="id_tipo_producto" class="form-control">
                                <option value="">Todos los tipos</option>
                                @foreach ($tiposProducto as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('id_tipo_producto') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.productos.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="section-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card table-card">
                <div class="card-body table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Nombre</th>
                                <th>Color</th>
                                <th>Proveedor</th>
                                <th>Tipo</th>
                                @if($veCostos)
                                <th>Precio</th>
                                @endif
                                <th>Precio público</th>
                                @foreach($camposDinamicos as $campo => $etiqueta)
                                    <th>{{ $etiqueta }}</th>
                                @endforeach
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $columnasTabla = 7 + ($veCostos ? 1 : 0) + count($camposDinamicos) + 1;
                            @endphp
                            @forelse ($productos as $producto)
                                @php
                                    $esCortinero = $producto->id_tipo_producto == 1 || strtolower($producto->tipoProducto->nombre ?? '') === 'cortinero';
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.productos.show', $producto->id) }}" class="record-link">
                                            {{ $producto->clave ?: '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.productos.show', $producto->id) }}" class="record-link">
                                            {{ $producto->nombre }}
                                        </a>
                                    </td>
                                    <td>{{ $producto->color ?: '-' }}</td>
                                    <td>{{ $producto->proveedor->nombre ?? 'N/A' }}</td>
                                    <td>{{ $producto->tipoProducto->nombre ?? 'Sin tipo' }}</td>
                                    @if($veCostos)
                                    <td class="money-value">
                                        {{ $producto->precio !== null ? '$' . number_format((float) $producto->precio, 2) : '-' }}
                                    </td>
                                    @endif
                                    <td class="money-value">
                                        {{ $producto->precio_publico !== null ? '$' . number_format((float) $producto->precio_publico, 2) : '-' }}
                                    </td>
                                    @foreach($camposDinamicos as $campo => $etiqueta)
                                        <td>{{ $producto->$campo ?: '-' }}</td>
                                    @endforeach
                                    <td>
                                        <div class="actions-wrap">
                                            <a href="{{ route('admin.productos.edit', $producto->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $columnasTabla }}" class="text-center text-muted py-4">No se encontraron productos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $productos->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="import-form" action="{{ route('admin.productos.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importar Productos desde Excel / CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    La primera fila del archivo debe contener los encabezados:
                    <strong>clave</strong>, <strong>nombre</strong> (obligatorio), <strong>precio</strong>,
                    <strong>precio_publico</strong>, <strong>color</strong>, <strong>descripcion</strong>,
                    <strong>proveedor</strong> (obligatorio), y los campos adicionales <strong>campo1</strong> a <strong>campo10</strong> según el tipo.
                    Si el proveedor no existe, se creará automáticamente.
                    Cada fila del archivo se registra como un producto nuevo; los productos existentes no se modifican.
                    Se recomienda guardar el archivo como <strong>.xlsx</strong> o <strong>.csv</strong>.
                </p>
                <div class="form-group">
                    <label class="field-label">Tipo de Producto</label>
                    <select name="id_tipo_producto" id="import-tipo-producto" class="form-control" required>
                        @foreach($tiposProducto as $tipo)
                            @php
                                $camposImportacion = $tipo->camposPersonalizados();
                            @endphp
                            <option value="{{ $tipo->id }}" data-campos='@json($camposImportacion)'>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="alert alert-light border small mb-3" id="import-campos-tipo">
                    Campos adicionales para este tipo: ninguno definido.
                </div>
                <div class="form-group mb-0">
                    <label class="field-label">Archivo Excel / CSV</label>
                    <input type="file" name="archivo" class="form-control-file" required accept=".xlsx,.xls,.csv,.xml,.txt">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal" id="import-cancel-btn">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="import-submit-btn">
                    <span class="import-btn-default"><i class="fas fa-file-import mr-1"></i> Importar</span>
                    <span class="import-btn-loading d-none"><i class="fas fa-spinner fa-spin mr-1"></i> Importando...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="import-loading-overlay" class="import-loading-overlay d-none" aria-live="polite" aria-busy="true">
    <div class="import-loading-box">
        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3 d-block"></i>
        <h5 class="mb-2">Importando productos</h5>
        <p class="text-muted mb-0 small">Por favor espere, esto puede tardar unos momentos...</p>
    </div>
</div>
@endsection

@section('scripts')
<style>
.import-loading-overlay {
    position: fixed;
    inset: 0;
    z-index: 2000;
    background: rgba(255, 255, 255, 0.85);
    display: flex;
    align-items: center;
    justify-content: center;
}

.import-loading-box {
    text-align: center;
    padding: 2rem 2.5rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12);
    max-width: 360px;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const importForm = document.getElementById('import-form');
    const importSubmitBtn = document.getElementById('import-submit-btn');
    const importCancelBtn = document.getElementById('import-cancel-btn');
    const importLoadingOverlay = document.getElementById('import-loading-overlay');
    const importTipoSelect = document.getElementById('import-tipo-producto');
    const importCamposTipo = document.getElementById('import-campos-tipo');
    let importEnviando = false;

    function mostrarCargaImportacion() {
        if (!importSubmitBtn || !importLoadingOverlay) {
            return;
        }

        importSubmitBtn.disabled = true;
        importSubmitBtn.querySelector('.import-btn-default').classList.add('d-none');
        importSubmitBtn.querySelector('.import-btn-loading').classList.remove('d-none');

        if (importCancelBtn) {
            importCancelBtn.disabled = true;
        }

        importLoadingOverlay.classList.remove('d-none');
    }

    if (importForm) {
        importForm.addEventListener('submit', function (event) {
            if (importEnviando) {
                event.preventDefault();
                return;
            }

            importEnviando = true;
            mostrarCargaImportacion();
        });
    }

    function actualizarCamposImportacion() {
        if (!importTipoSelect || !importCamposTipo) {
            return;
        }

        const selectedOption = importTipoSelect.options[importTipoSelect.selectedIndex];
        let campos = {};

        try {
            campos = JSON.parse(selectedOption.getAttribute('data-campos') || '{}');
        } catch (e) {}

        const etiquetas = Object.entries(campos)
            .map(([campo, etiqueta]) => `<strong>${campo}</strong> (${etiqueta})`)
            .join(', ');

        importCamposTipo.innerHTML = etiquetas
            ? `Campos adicionales para este tipo: ${etiquetas}`
            : 'Campos adicionales para este tipo: ninguno definido.';
    }

    if (importTipoSelect) {
        importTipoSelect.addEventListener('change', actualizarCamposImportacion);
        actualizarCamposImportacion();
    }
});
</script>
@endsection
