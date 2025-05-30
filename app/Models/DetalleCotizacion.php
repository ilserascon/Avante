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

        'total_tela',
        'precio_m2_tela',
        'descripcion_tela',
        'total_tela_final',

        'total_tergal',
        'precio_m2_tergal',
        'descripcion_tergal',
        'total_tergal_final',

        'total_forro',
        'precio_m2_forro',
        'descripcion_forro',
        'total_final_forro',

        'costo_total_tela_tergal_forro',

        'm2_1',
        'costo_mano_obra_1',
        'total_mano_obra_1',

        'm2_2',
        'costo_mano_obra_2',
        'total_mano_obra_2',

        'costo_total_mano_obra',

    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
}
