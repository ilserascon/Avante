@extends('layouts.stisla')

@section('title', 'Cotizaciones')

@section('content')
<style>
    .cotizaciones-page .hero-card {
        border: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
        box-shadow: 0 10px 25px rgba(43, 65, 98, 0.08);
    }

    .cotizaciones-page .hero-card h1 {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: #26344d;
    }

    .cotizaciones-page .hero-subtitle {
        color: #6b7b95;
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    .cotizaciones-page .filter-card,
    .cotizaciones-page .table-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(21, 38, 63, 0.08);
    }

    .cotizaciones-page .filter-card .card-body {
        padding: 1rem 1.25rem;
    }

    .cotizaciones-page .table thead th {
        background: #f5f8ff;
        color: #4a5f83;
        font-weight: 700;
        border-bottom: 0;
        white-space: nowrap;
    }

    .cotizaciones-page .table tbody td {
        vertical-align: middle;
    }

    .cotizaciones-page .table tbody tr{
        transition: all .25s ease;
        cursor:pointer;
    }

    .cotizaciones-page .table tbody tr:hover{
        background:#f8fbff;
        transform:translateY(-2px);
        box-shadow:0 3px 10px rgba(0,0,0,.05);
    }

    .cotizaciones-page .table tbody tr:hover td{
        color:#243b63;
    }

    .cotizaciones-page .cot-total {
        font-weight: 700;
        color: #1f3a69;
    }

    .cotizaciones-page .status-chip {
        display: inline-block;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        text-transform: uppercase;
    }

    .cotizaciones-page .status-solicitada {
        color: #8b5e00;
        background: #fff4d9;
    }

    .cotizaciones-page .status-aceptada {
        color: #166534;
        background: #dcfce7;
    }

    .cotizaciones-page .status-rechazada {
        color: #991b1b;
        background: #fee2e2;
    }

    .cotizaciones-page .status-completada {
        color: #1e40af;
        background: #dbeafe;
    }

    .cotizaciones-page .status-cancelada {
        color: #9a3412;
        background: #ffedd5;
    }

    .cotizaciones-page .actions-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .cotizaciones-page .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        transition: all 0.15s ease-in-out;
    }

    .cotizaciones-page .action-btn i {
        font-size: 0.85rem;
    }

    .cotizaciones-page .action-btn:hover {
        transform: translateY(-1px);
    }

    .cotizaciones-page .btn-see {
        background: #e7f1ff;
        color: #1d4ed8;
        border-color: #cfe2ff;
    }

    .cotizaciones-page .btn-edit {
        background: #fff5df;
        color: #b45309;
        border-color: #ffe3ae;
    }

    .cotizaciones-page .btn-pdf {
        background: #f1efff;
        color: #5b3cc4;
        border-color: #ddd6fe;
    }

    .cotizaciones-page .btn-accept {
        background: #e8f9ee;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .cotizaciones-page .btn-reject {
        background: #ffecec;
        color: #dc2626;
        border-color: #fecaca;
    }

    .cotizaciones-page .btn-complete {
        background: #e0ecff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .cotizaciones-page .btn-cancel {
        background: #fff1e6;
        color: #c2410c;
        border-color: #fed7aa;
    }

    @media (max-width: 767.98px) {
        .cotizaciones-page .hero-actions {
            margin-top: 0.75rem;
            width: 100%;
        }

        .cotizaciones-page .hero-actions .btn {
            width: 100%;
        }
    }
    .btn-pdf-cliente{
        background:#fff0f0;
        color:#dc3545;
        border:1px solid #ffc9c9;
    }
    .btn-pdf-decorador{
        background:#f2ecff;
        color:#6f42c1;
        border:1px solid #d8c8ff;
    }
    .cotizacion-link{
        font-weight:700;
        color:#4e73df;
        text-decoration:none;
        transition:.2s;
    }

    .cotizacion-link:hover{
        color:#1a8683;
        text-decoration:none;
    }

    .cotizaciones-page .table tbody tr:hover .cotizacion-link{
        color:#1a8683;
    }

    .cotizaciones-page .table tbody tr:hover td {
        background: #d5dedc;
    }
    
</style>
<div class="section">
    <div class="cotizaciones-page">
        <div class="card hero-card mb-4">
            <div class="card-body py-3 px-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <div>
                        <h3>Cotizaciones</h3>
                    </div>
                    <div class="hero-actions">
                        @if(auth()->user()->puedeEditarCotizacion())
                            <a href="{{ route('admin.cotizaciones.create') }}" class="btn btn-primary px-4">
                                <i class="fas fa-plus mr-1"></i> Nueva Cotización
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.cotizaciones.index') }}" class="mb-4">
            <div class="card filter-card">
                <div class="card-body">
                    <div class="form-row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Folio</label>
                            <input type="text" name="folio" value="{{ request('folio') }}" class="form-control" placeholder="Ej. 00123">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Cliente</label>
                            <input type="text" name="cliente" value="{{ request('cliente') }}" class="form-control" placeholder="Buscar por nombre">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Estatus</label>
                            <select name="estatus" class="form-control">
                                <option value="">Todos los estatus</option>
                                <option value="solicitada" {{ request('estatus') == 'solicitada' ? 'selected' : '' }}>Solicitada</option>
                                <option value="aceptada" {{ request('estatus') == 'aceptada' ? 'selected' : '' }}>Aceptada</option>
                                <option value="completada" {{ request('estatus') == 'completada' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelada" {{ request('estatus') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                <option value="rechazada" {{ request('estatus') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row align-items-end mt-md-2">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control" placeholder="Desde">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="small font-weight-bold text-muted mb-1">Fecha Fin</label>
                            <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control" placeholder="Hasta">
                        </div>
                        <div class="col-md-4 d-flex mb-2 mb-md-0">
                            <button type="submit" class="btn btn-primary mr-2 flex-grow-1">Buscar</button>
                            <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-light border flex-grow-1">Limpiar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="section-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">{!! session('warning') !!}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{!! session('error') !!}</div>
            @endif

            <div class="card table-card">
                <div class="card-body table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estatus</th>
                                <th>Creado por</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cotizaciones as $cotizacion)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.cotizaciones.show',$cotizacion->id) }}"
                                        class="cotizacion-link">
                                            {{ str_pad($cotizacion->id,5,'0',STR_PAD_LEFT) }}
                                        </a>
                                    </td>
                                    <td>{{ $cotizacion->cliente ? $cotizacion->cliente->nombre : 'N/A' }}</td>
                                    <td>{{ $cotizacion->fecha ? \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y') : '-' }}</td>
                                    <td class="cot-total">${{ number_format((float) ($cotizacion->precio_publico ?? 0), 2) }}</td>
                                    <td>
                                        @php
                                            $estatus = strtolower((string) $cotizacion->estatus);
                                            $statusClass = in_array($estatus, ['solicitada', 'aceptada', 'rechazada', 'completada', 'cancelada']) ? 'status-' . $estatus : 'status-solicitada';
                                        @endphp
                                        <span class="status-chip {{ $statusClass }}">{{ ucfirst($cotizacion->estatus) }}</span>
                                    </td>
                                    <td>{{ $cotizacion->creadoPor?->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="actions-wrap justify-content-end">
                                        @if(auth()->user()->puedeEditarCotizacion() && $cotizacion->estatus === 'solicitada')
                                            <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(!in_array($cotizacion->estatus, ['rechazada', 'cancelada']))
                                            <a href="{{ route('admin.cotizaciones.pdf', $cotizacion->id) }}" class="action-btn btn-pdf" title="PDF Cliente" target="_blank">
                                                <i class="fas fa-file-pdf btn-pdf-cliente"></i>
                                            </a>
                                            @if(auth()->user()->puedeVerPdfDecorador())
                                                <a href="{{ route('admin.cotizaciones.pdf-decorador', $cotizacion->id) }}" class="action-btn btn-pdf" title="PDF Decorador" target="_blank">
                                                    <i class="fas fa-file-pdf btn-pdf-decorador"></i>
                                                </a>
                                            @endif
                                            @if(auth()->user()->puedeGestionarEstatusCotizacion())
                                            @if($cotizacion->estatus === 'solicitada')
                                                <form action="{{ route('admin.cotizaciones.cambiar-estatus', $cotizacion->id) }}" method="POST" class="mb-0 d-inline js-cotizacion-estatus-form">
                                                    @csrf
                                                    <input type="hidden" name="estatus" value="aceptada">
                                                    <button type="submit" class="action-btn btn-accept" title="Aceptar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.cotizaciones.cambiar-estatus', $cotizacion->id) }}" method="POST" class="mb-0 d-inline js-cotizacion-estatus-form">
                                                    @csrf
                                                    <input type="hidden" name="estatus" value="rechazada">
                                                    <button type="submit" class="action-btn btn-reject" title="Rechazar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @elseif($cotizacion->estatus === 'aceptada')
                                                <form action="{{ route('admin.cotizaciones.cambiar-estatus', $cotizacion->id) }}" method="POST" class="mb-0 d-inline js-cotizacion-estatus-form">
                                                    @csrf
                                                    <input type="hidden" name="estatus" value="completada">
                                                    <button type="submit" class="action-btn btn-complete" title="Completar">
                                                        <i class="fas fa-box-open"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.cotizaciones.cambiar-estatus', $cotizacion->id) }}" method="POST" class="mb-0 d-inline js-cotizacion-estatus-form">
                                                    @csrf
                                                    <input type="hidden" name="estatus" value="cancelada">
                                                    <button type="submit" class="action-btn btn-cancel" title="Cancelar">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @endif
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No hay cotizaciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $cotizaciones->links('pagination::bootstrap-5') }}
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
    document.querySelectorAll('.js-cotizacion-estatus-form').forEach(function (form) {
        var enviando = false;

        form.addEventListener('submit', function (event) {
            if (enviando) {
                return;
            }

            event.preventDefault();

            var estatus = form.querySelector('[name="estatus"]').value;
            var esAceptar = estatus === 'aceptada';
            var esCompletar = estatus === 'completada';
            var esRechazar = estatus === 'rechazada';
            var esCancelar = estatus === 'cancelada';

            var textoConfirmacion = '¿Desea actualizar el estatus de esta cotización?';
            var textoBoton = 'Sí, confirmar';
            var esPeligroso = false;

            if (esAceptar) {
                textoConfirmacion = '¿Desea aceptar esta cotización?';
                textoBoton = 'Sí, aceptar';
            } else if (esCompletar) {
                textoConfirmacion = '¿Desea completar esta cotización? Se validará y descontará el inventario.';
                textoBoton = 'Sí, completar';
            } else if (esCancelar) {
                textoConfirmacion = '¿Desea cancelar esta cotización aceptada?';
                textoBoton = 'Sí, cancelar';
                esPeligroso = true;
            } else if (esRechazar) {
                textoConfirmacion = '¿Desea rechazar esta cotización?';
                textoBoton = 'Sí, rechazar';
                esPeligroso = true;
            }

            swal({
                title: '¿Está seguro?',
                text: textoConfirmacion,
                icon: 'warning',
                buttons: ['No', textoBoton],
                dangerMode: esPeligroso,
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
