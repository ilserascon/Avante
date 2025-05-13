<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'tipo',
        'lleva_forro',
        'detalle',
        'total'
    ];

    protected $casts = [
        'detalle' => 'array',
        'lleva_forro' => 'boolean'
    ];
}

