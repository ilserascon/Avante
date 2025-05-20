<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'rfc',
        'razon_social',
        'telefono',
        'email',
        'direccion',
        'codigo_postal',
        'borrado'
    ];

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }
}
