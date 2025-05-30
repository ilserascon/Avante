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

        'tergal_id',
        'ancho_tergal',
        'ancho_tergal_real',
        'largo_tergal',
        'no_lienzos_tergal',
        'no_lienzos_redondeado_tergal',
        'bastilla_tergal',

        'forro_id',
        'ancho_forro',
        'ancho_forro_real',
        'largo_forro',
        'no_lienzos_forro',
        'no_lienzos_redondeado_forro',
        'bastilla_forro',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
}
