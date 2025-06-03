@extends('layouts.stisla')

@section('title', 'Editar Tipo de Insumo')

@section('content')
<div class="section">
    <div class="section-header">
        <h1>Editar Tipo de Insumo</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Formulario de Edición</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tipo-insumos.update', $tipoInsumo->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $tipoInsumo->nombre) }}" required>
                    </div>

                    <div class="form-row">
                        @for($i = 1; $i <= 15; $i++)
                            <div class="form-group col-md-4">
                                <label for="campo{{ $i }}">Campo {{ $i }}</label>
                                <input type="text" id="campo{{ $i }}" name="campo{{ $i }}" class="form-control" value="{{ old('campo'.$i, $tipoInsumo->{'campo'.$i}) }}">
                            </div>
                            @if($i % 3 == 0 && $i < 15)
                                </div><div class="form-row">
                            @endif
                        @endfor
                    </div>

                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('admin.tipo-insumos.index') }}" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection