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

    public function telefonoWhatsApp(): ?string
    {
        return self::normalizarTelefonoWhatsApp($this->telefono);
    }

    public static function normalizarTelefonoWhatsApp(?string $telefono): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $telefono ?? '');
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '52')) {
            if (strlen($digits) === 13 && str_starts_with($digits, '521')) {
                return '52' . substr($digits, 3);
            }

            return $digits;
        }

        if (strlen($digits) === 10) {
            return '52' . $digits;
        }

        return $digits;
    }
}
