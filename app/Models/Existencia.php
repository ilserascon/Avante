<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Existencia extends Model {
    use HasFactory;
    protected $table = 'existencia';
    protected $fillable = ['id_almacen', 'id_producto', 'id_insumo', 'cantidad'];

        public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo');
    }
}
