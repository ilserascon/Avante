<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        // otros campos si los hay
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'producto_insumo', 'id_producto', 'id_insumo')
                    ->withPivot('cantidad', 'created_at', 'updated_at');
    }
    
    public function entradas()
    {
        return $this->belongsToMany(Entrada::class, 'detalle_entradas', 'id_producto', 'id_entrada')
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
    }
    
}