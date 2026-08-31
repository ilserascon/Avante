<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoInsumo extends Model
{
    public const CAMPOS_DINAMICOS = 15;

    protected $table = 'tipo_insumo';

    protected $fillable = [
        'nombre',
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
    ];

    protected static function booted()
    {
        static::updated(function ($tipoInsumo) {
            $insumos = Insumo::where('id_tipo_insumo', $tipoInsumo->id)->get();

            foreach ($insumos as $insumo) {
                foreach ($tipoInsumo->getAttributes() as $campo => $valor) {
                    if (str_starts_with($campo, 'campo')) {
                        $insumo->$campo = $valor;
                    }
                }
                $insumo->save();
            }
        });
    }

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public const NOMBRE_MANO_OBRA = 'Mano de Obra';

    public function scopeExceptManoDeObra($query)
    {
        return $query->where('nombre', '!=', self::NOMBRE_MANO_OBRA);
    }

    /** @return array<string, string> campoN => etiqueta configurada */
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