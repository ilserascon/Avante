<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumo';

    protected $fillable = [
        'nombre',
        'id_tipo_insumo',
        'id_proveedor',
        'costo',
        'precio_publico',
        'utilidad',
        'campo1',
        'campo2',
        'campo3',
        'campo4',
        'campo5',
        'campo6',
        'campo7',
        'campo8',
        'campo9',
        'campo10',
        'campo11',
        'campo12',
        'campo13',
        'campo14',
        'campo15'
    ];

    public $timestamps = false;

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_insumo', 'id_insumo', 'id_producto')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function tipoInsumo()
    {
        return $this->belongsTo(TipoInsumo::class, 'id_tipo_insumo');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function cotizaciones()
    {
        return $this->belongsToMany(Cotizacion::class, 'cotizacion_insumo')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
}
