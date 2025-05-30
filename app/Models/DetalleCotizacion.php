<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{

    protected $table = 'detalle_cotizacion';

    protected $fillable = [
        'cotizacion_id',
        'tela_id',
        'ancho_tela',
        'ancho',
        'largo',
        'no_lienzos',
        'no_lienzos_redondeado',
        'bastilla',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
}
