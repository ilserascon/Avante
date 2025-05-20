<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEntrada extends Model {
    use HasFactory;
    protected $fillable = ['id_entrada', 'id_producto', 'id_insumo', 'cantidad'];

    public function entrada() {
        return $this->belongsTo(Entrada::class, 'id_entrada');
    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function insumo() {
        return $this->belongsTo(Insumo::class, 'id_insumo', 'id');
        }
}
