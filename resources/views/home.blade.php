@extends('layouts.stisla')

@section('title', 'Inicio')

@section('content')

<style>
.dashboard-card{
    border:none;
    border-radius:15px;
    transition:.25s;
    cursor:pointer;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}

.dashboard-card:hover{
    transform:translateY(-6px);
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.dashboard-icon{
    width:65px;
    height:65px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    color:#fff;
    margin:auto;
}

.bg-clientes{
    background:#4e73df;
}

.bg-insumos{
    background:#dfd14f;
}

.bg-productos{
    background:#6f42c1;
}

.bg-almacen{
    background:#fd7e14;
}

.bg-cotizaciones{
    background:#20c997;
}

.bg-nueva{
    background:#dc3545;
}

.stat-card{
    border:none;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,.08);
}

.dashboard-card a{
    text-decoration:none;
    color:inherit;
}

.quick-btn{
    border-radius:10px;
}
</style>

<div class="section">
    <br><br>

    <div class="section-body">

        <div class="row">
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <a href="{{ route('admin.cotizaciones.create') }}">
                        <div class="card-body text-center">

                            <div class="dashboard-icon bg-nueva">
                                <i class="fas fa-calculator"></i>
                            </div>

                            <h5 class="mt-3">Crear Cotización</h5>

                            <small class="text-muted">
                                Iniciar una nueva cotización
                            </small>

                        </div>
                    </a>
                </div>
            </div>
        
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <a href="{{ route('admin.cotizaciones.index') }}">
                        <div class="card-body text-center">

                            <div class="dashboard-icon bg-cotizaciones">
                                <i class="fas fa-list"></i>
                            </div>

                            <h5 class="mt-3">Cotizaciones</h5>

                            <small class="text-muted">
                                Historial de cotizaciones
                            </small>

                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <a href="{{ route('admin.clientes.index') }}">
                        <div class="card-body text-center">

                            <div class="dashboard-icon bg-clientes">
                                <i class="fas fa-user-friends"></i>
                            </div>

                            <h5 class="mt-3">Clientes</h5>

                            <small class="text-muted">
                                Administrar clientes
                            </small>

                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <a href="{{ route('admin.insumos.index') }}">
                        <div class="card-body text-center">

                            <div class="dashboard-icon bg-insumos">
                                <i class="fas fa-boxes"></i>
                            </div>

                            <h5 class="mt-3">Insumos</h5>

                            <small class="text-muted">
                                Catálogo de insumos
                            </small>

                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <a href="{{ route('admin.productos.index') }}">
                        <div class="card-body text-center">

                            <div class="dashboard-icon bg-productos">
                                <i class="fas fa-box-open"></i>
                            </div>

                            <h5 class="mt-3">Productos</h5>

                            <small class="text-muted">
                                Catálogo de productos
                            </small>

                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card dashboard-card">
                    <a href="{{ route('admin.almacenes.index') }}">
                        <div class="card-body text-center">

                            <div class="dashboard-icon bg-almacen">
                                <i class="fas fa-warehouse"></i>
                            </div>

                            <h5 class="mt-3">Inventario</h5>

                            <small class="text-muted">
                                Almacenes e inventario
                            </small>

                        </div>
                    </a>
                </div>
            </div>


        </div>

        <div class="row mt-3">

            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header">
                        <h4>Últimas Cotizaciones</h4>
                    </div>

                    <div class="card-body">

                        <table class="table table-striped">

                            <thead>

                                <tr>
                                    <th>Folio</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>

                            </thead>

                            <tbody>

                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Aún no hay información.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card stat-card mb-3">
                    <div class="card-body text-center">
                        <h3>0</h3>
                        <small>Clientes</small>
                    </div>
                </div>

                <div class="card stat-card mb-3">
                    <div class="card-body text-center">
                        <h3>0</h3>
                        <small>Productos</small>
                    </div>
                </div>

                <div class="card stat-card mb-3">
                    <div class="card-body text-center">
                        <h3>0</h3>
                        <small>Insumos</small>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h3>0</h3>
                        <small>Cotizaciones</small>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
