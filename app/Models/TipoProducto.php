<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    public const CAMPOS_DINAMICOS = 10;

    protected $table = 'tipo_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
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
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_tipo_producto');
    }

    /** Las persianas se cotizan por metro cuadrado en lugar de por pieza. */
    public function esPersiana(): bool
    {
        return $this->nombre !== null && mb_stripos($this->nombre, 'persiana') !== false;
    }

    public function camposPersonalizados(): array
    {
        $campos = [];

        for ($i = 1; $i <= self::CAMPOS_DINAMICOS; $i++) {
            $campo = 'campo' . $i;
            if (!empty($this->$campo)) {
                $campos[$campo] = $this->$campo;
            }
        }

        return $campos;
    }
}
