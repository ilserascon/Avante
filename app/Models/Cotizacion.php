<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{

    protected $table = 'cotizaciones';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'area',
        'lleva_cortina',
        'lleva_tergal',
        'lleva_forro',
        'total_lienzos',
        'total_m2_forro',
        'total_m2_tela',
        'total_m2_tergal',
        'costo_cortina',
        'utilidad',
        'costo_decorador',
        'precio_publico',
        'estatus',
        'aplicar_iva',
        'descuento',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'cotizacion_insumo')
            ->withPivot('cantidad', 'precio_unitario', 'descuento', 'subtotal')
            ->withTimestamps();
    }

    public function detalleCotizacion()
    {
        return $this->hasOne(DetalleCotizacion::class);
    }

    public function detallesCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'cotizacion_producto')
            ->withPivot('cantidad', 'ancho', 'largo', 'precio_unitario', 'descuento', 'subtotal')
            ->withTimestamps();
    }

    public function shareToken(): string
    {
        return substr(hash_hmac('sha256', 'cotizacion-pdf:' . $this->id, config('app.key')), 0, 40);
    }

    public function shareUrl(): string
    {
        return route('cotizaciones.compartir', [
            'cotizacion' => $this->id,
            'token' => $this->shareToken(),
        ]);
    }
}
