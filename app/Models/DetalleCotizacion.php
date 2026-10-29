<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{

    protected $table = 'detalle_cotizacion';

    protected $fillable = [
        'cotizacion_id',
        'lleva_cortina',
        'lleva_tergal',
        'lleva_forro',
        'tela_id',
        'ancho_tela',
        'ancho',
        'largo',
        'no_lienzos',
        'no_lienzos_redondeado',
        'bastilla',
        'descripcion',
        'area',

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

        'total_lienzos',
        'total_m2_tela',
        'total_m2_tergal',
        'total_m2_forro',
        'costo_cortina',

        'decorador_porcentaje',
        'descuento',

        'cortinero_id',
        'cortinero_cantidad',
        'cortinero_precio',
        'cortinero_tergal_id',
        'cortinero_tergal_cantidad',
        'cortinero_tergal_precio',

        'materiales_varios',

        'techo_muro',
        'corredera_id',
        'cinta_id',
        'bastilla_taller',
        'confeccion',
        'altura_izq',
        'altura_centro',
        'altura_der',
        'vuelta_lateral',
        'observacion_taller',
        'num_bastones',
        'medida_baston',
        'ancho_fabricacion',
        'largo_fabricacion',
        'cinta_onda',
        'apertura',
        'control',
        'ancho_fabricacion_tergal',
        'largo_fabricacion_tergal',
        'cinta_onda_tergal',
        'apertura_tergal',
        'control_tergal',

    ];

    protected $casts = [
        'materiales_varios' => 'array',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function tela()
    {
        return $this->belongsTo(Insumo::class, 'tela_id');
    }

    public function tergal()
    {
        return $this->belongsTo(Insumo::class, 'tergal_id');
    }

    public function forro()
    {
        return $this->belongsTo(Insumo::class, 'forro_id');
    }

    public function cortinero()
    {
        return $this->belongsTo(Producto::class, 'cortinero_id');
    }

    public function corredera()
    {
        return $this->belongsTo(Insumo::class, 'corredera_id');
    }

    public function cinta()
    {
        return $this->belongsTo(Insumo::class, 'cinta_id');
    }

    public function cortineroTergal()
    {
        return $this->belongsTo(Producto::class, 'cortinero_tergal_id');
    }
}
