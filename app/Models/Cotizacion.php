<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{

    protected $table = 'cotizaciones';

    protected $fillable = [
        'cliente_id', 'fecha',
        'lleva_cortina', 'lleva_tergal', 'lleva_forro',
        'total_lienzos', 'total_m2_forro', 'total_m2_tela', 'total_m2_tergal',
        'costo_cortina', 'utilidad', 'costo_decorador', 'precio_publico',
        'estatus'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'cotizacion_insumo')
                    ->withPivot('cantidad', 'precio_unitario', 'subtotal')
                    ->withTimestamps();
    }
}


