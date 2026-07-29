<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumo';

    protected $fillable = [
        'nombre',
        'clave',
        'color',
        'id_tipo_insumo',
        'id_proveedor',
        'costo',
        'precio_publico',
        'utilidad',
        'campo1',
        'campo2',
        'campo3',
        'campo4',
        'campo5',
        'campo6',
        'campo7',
        'campo8',
        'campo9',
        'campo10',
        'campo11',
        'campo12',
        'campo13',
        'campo14',
        'campo15',
        'borrado',
    ];


    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_insumo', 'id_insumo', 'id_producto')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function tipoInsumo()
    {
        return $this->belongsTo(TipoInsumo::class, 'id_tipo_insumo');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function cotizaciones()
    {
        return $this->belongsToMany(Cotizacion::class, 'cotizacion_insumo')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    public static function normalizarCampoMostrar($valor): string
    {
        if ($valor === null) {
            return '';
        }

        $texto = trim((string) $valor);
        if ($texto === '' || strcasecmp($texto, 'null') === 0) {
            return '';
        }

        return $texto;
    }

    public function campo1Mostrar(): string
    {
        return self::normalizarCampoMostrar($this->campo1);
    }

    public function campo2Mostrar(): string
    {
        return self::normalizarCampoMostrar($this->campo2);
    }

    /** Etiqueta para selects de tela, tergal y forro en cotizaciones. */
    public function etiquetaMaterialTextil(): string
    {
        $partes = array_filter([
            self::normalizarCampoMostrar($this->clave),
            self::normalizarCampoMostrar($this->nombre),
            self::normalizarCampoMostrar($this->color),
            $this->campo1Mostrar(),
        ]);

        return implode(' - ', $partes);
    }

    /** Etiqueta clave - nombre para selects generales de insumos. */
    public function etiquetaClaveNombre(): string
    {
        $partes = array_filter([
            self::normalizarCampoMostrar($this->clave),
            self::normalizarCampoMostrar($this->nombre),
        ]);

        return implode(' - ', $partes);
    }

    public function getNombreCompletoAttribute()
    {
        $proveedor = $this->proveedor ? $this->proveedor->nombre : '';

        return trim(implode(' | ', array_filter([
            self::normalizarCampoMostrar($this->nombre),
            $this->campo1Mostrar(),
            $this->campo2Mostrar(),
            self::normalizarCampoMostrar($proveedor),
        ])));
    }
}
