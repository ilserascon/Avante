<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $table = 'entradas';

    protected $fillable = [
        'id_almacen',
        'id_usuario',
    ];


    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen');
    }


    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }


    public function detalles()
    {
        return $this->hasMany(DetalleEntrada::class, 'id_entrada');
    }
}