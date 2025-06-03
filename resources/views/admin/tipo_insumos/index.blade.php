@extends('layouts.stisla')

@section('title', 'Lista de Tipos de Insumo')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Lista de Tipos de Insumo</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.tipo-insumos.create') }}" class="btn btn-primary">Nuevo Tipo de Insumo</a>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Lista de Tipos de Insumo</h4>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Campo 1</th>
                                    <th>Campo 2</th>
                                    <th>Campo 3</th>
                                    <th>Campo 4</th>
                                    <th>Campo 5</th>
                                    <th>Campo 6</th>
                                    <th>Campo 7</th>
                                    <th>Campo 8</th>
                                    <th>Campo 9</th>
                                    <th>Campo 10</th>
                                    <th>Campo 11</th>
                                    <th>Campo 12</th>
                                    <th>Campo 13</th>
                                    <th>Campo 14</th>
                                    <th>Campo 15</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tipoInsumos as $tipoInsumo)
                                    <tr>
                                        <td>{{ $tipoInsumo->nombre }}</td>
                                        <td>{{ $tipoInsumo->campo1 }}</td>
                                        <td>{{ $tipoInsumo->campo2 }}</td>
                                        <td>{{ $tipoInsumo->campo3 }}</td>
                                        <td>{{ $tipoInsumo->campo4 }}</td>
                                        <td>{{ $tipoInsumo->campo5 }}</td>
                                        <td>{{ $tipoInsumo->campo6 }}</td>
                                        <td>{{ $tipoInsumo->campo7 }}</td>
                                        <td>{{ $tipoInsumo->campo8 }}</td>
                                        <td>{{ $tipoInsumo->campo9 }}</td>
                                        <td>{{ $tipoInsumo->campo10 }}</td>
                                        <td>{{ $tipoInsumo->campo11 }}</td>
                                        <td>{{ $tipoInsumo->campo12 }}</td>
                                        <td>{{ $tipoInsumo->campo13 }}</td>
                                        <td>{{ $tipoInsumo->campo14 }}</td>
                                        <td>{{ $tipoInsumo->campo15 }}</td>
                                        <td>
                                            <a href="{{ route('admin.tipo-insumos.edit', $tipoInsumo->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $tipoInsumos->appends(request()->query())->links('pagination::bootstrap-4') }}                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection