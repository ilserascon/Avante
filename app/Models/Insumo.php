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

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    // Relaciones
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

    // Accesor
    public function getNombreCompletoAttribute()
    {
        $proveedor = $this->proveedor ? $this->proveedor->nombre : '';
        return trim(implode(' | ', array_filter([
            $this->nombre,
            $this->campo1,
            $this->campo2,
            $proveedor
        ])));
    }
}
