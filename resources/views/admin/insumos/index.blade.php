@extends('layouts.stisla')

@section('title', 'Insumos')

@section('content')
@include('admin.partials.professional-styles')
@php $veCostos = auth()->user()?->vePreciosInternosCatalogo() ?? false; @endphp

<div class="section">
    <div class="admin-pro">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Insumos</h3>
                        <p class="hero-subtitle">Gestione el catálogo de insumos por tipo.</p>
                    </div>
                    <div class="hero-actions d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.insumos.create') }}" class="btn btn-primary px-4">
                            <i class="fas fa-plus mr-1"></i> Nuevo Insumo
                        </a>&nbsp;
                        <button class="btn btn-success px-4" data-toggle="modal" data-target="#importModal">
                            <i class="fas fa-file-import mr-1"></i> Importar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.insumos.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="field-label">Nombre o clave</label>
                            <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control" placeholder="Buscar por nombre o clave">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="field-label">Tipo de insumo</label>
                            <select name="tipo_insumo" class="form-control">
                                <option value="">Todos los tipos</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('tipo_insumo') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="field-label">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="habilitado" {{ $estado == 'habilitado' ? 'selected' : '' }}>Habilitados</option>
                                <option value="inhabilitado" {{ $estado == 'inhabilitado' ? 'selected' : '' }}>Inhabilitados</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.insumos.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
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
                    @if(!$tipoSeleccionado)
                        <div class="empty-state">
                            <i class="fas fa-filter fa-2x d-block"></i>
                            <h5 class="mb-1">Seleccione un tipo de insumo</h5>
                            <p class="mb-0 small">Use el filtro de arriba para ver los campos correspondientes.</p>
                        </div>
                    @elseif($insumos->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-box-open fa-2x d-block"></i>
                            <h5 class="mb-1">No hay insumos registrados</h5>
                            <p class="mb-0 small">No se encontraron insumos para este tipo con los filtros aplicados.</p>
                        </div>
                    @else
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Clave</th>
                                    <th>Nombre</th>
                                    <th>Color</th>
                                    <th>Proveedor</th>
                                    @if($veCostos)
                                    <th>Costo</th>
                                    @endif
                                    <th>Precio Público</th>
                                    @if($veCostos)
                                    <th>Utilidad</th>
                                    @endif
                                    <th>Estado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insumos as $insumo)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.insumos.show', $insumo->id) }}" class="record-link">
                                                {{ $insumo->clave ?: '-' }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.insumos.show', $insumo->id) }}" class="record-link">
                                                {{ $insumo->nombre }}
                                            </a>
                                        </td>
                                        <td>{{ $insumo->color ?: '-' }}</td>
                                        <td>{{ $insumo->proveedor->nombre ?? 'N/A' }}</td>
                                        @if($veCostos)
                                        <td class="money-value">${{ number_format((float) $insumo->costo, 2) }}</td>
                                        @endif
                                        <td class="money-value">${{ number_format((float) $insumo->precio_publico, 2) }}</td>
                                        @if($veCostos)
                                        <td>{{ number_format((float) $insumo->utilidad, 2) }}</td>
                                        @endif
                                        <td>
                                            @if($insumo->borrado == 0)
                                                <span class="status-chip status-active">Activo</span>
                                            @else
                                                <span class="status-chip status-inactive">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="actions-wrap">
                                                @if($insumo->borrado == 0)
                                                    <a href="{{ route('admin.insumos.edit', $insumo->id) }}" class="action-btn btn-edit" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.insumos.destroy', $insumo->id) }}" method="POST" class="mb-0 d-inline js-insumo-estado-form" data-accion="inhabilitar">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-btn btn-delete" title="Inhabilitar">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.insumos.habilitar', $insumo->id) }}" method="POST" class="mb-0 d-inline js-insumo-estado-form" data-accion="habilitar">
                                                        @csrf
                                                        <button type="submit" class="action-btn btn-enable" title="Habilitar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $insumos->appends(request()->query())->links('pagination::bootstrap-5') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="import-form" action="{{ route('admin.insumos.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importar Insumos desde Excel / CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    La primera fila del archivo debe contener los encabezados. Columnas base:
                    <strong>clave</strong>, <strong>nombre</strong> (obligatorio), <strong>color</strong>,
                    <strong>proveedor</strong> (obligatorio), <strong>costo</strong>, <strong>precio_publico</strong>,
                    <strong>utilidad</strong>, y los campos adicionales <strong>campo1</strong> a <strong>campo15</strong> según el tipo.
                    Si el proveedor no existe, se creará automáticamente.
                    Si ya existe un insumo con el mismo tipo, clave, nombre, color, proveedor y campo1, se actualizará en lugar de duplicarse.
                    Los encabezados de campos adicionales pueden usar el nombre del campo (campo1) o la etiqueta configurada en el tipo (por ejemplo ANCHO, ARTICULO).
                    Se recomienda guardar el archivo como <strong>.xlsx</strong> o <strong>.csv</strong>.
                </p>
                <div class="form-group">
                    <label class="field-label">Tipo de Insumo</label>
                    <select name="id_tipo_insumo" id="import-tipo-insumo" class="form-control" required>
                        @foreach($tipos as $tipo)
                            @php
                                $camposImportacion = [];
                                for ($i = 1; $i <= 15; $i++) {
                                    $campo = 'campo' . $i;
                                    if (!empty($tipo->$campo)) {
                                        $camposImportacion[$campo] = $tipo->$campo;
                                    }
                                }
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
        <h5 class="mb-2">Importando insumos</h5>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert@2.1.2/dist/sweetalert.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const importForm = document.getElementById('import-form');
    const importSubmitBtn = document.getElementById('import-submit-btn');
    const importCancelBtn = document.getElementById('import-cancel-btn');
    const importLoadingOverlay = document.getElementById('import-loading-overlay');
    const importTipoSelect = document.getElementById('import-tipo-insumo');
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

    document.querySelectorAll('.js-insumo-estado-form').forEach(function (form) {
        var enviando = false;

        form.addEventListener('submit', function (event) {
            if (enviando) {
                return;
            }

            event.preventDefault();

            var esInhabilitar = form.getAttribute('data-accion') === 'inhabilitar';

            swal({
                title: '¿Está seguro?',
                text: esInhabilitar
                    ? '¿Desea inhabilitar este insumo?'
                    : '¿Desea habilitar este insumo?',
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
