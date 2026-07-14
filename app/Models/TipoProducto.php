<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    protected $table = 'tipo_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_tipo_producto');
    }
}
