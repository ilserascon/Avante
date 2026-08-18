<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'clave',
        'color',
        'descripcion',
        'id_proveedor',
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
        'precio',
        'precio_publico',
        'id_tipo_producto',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'producto_insumo', 'id_producto', 'id_insumo')
            ->withPivot('cantidad', 'created_at', 'updated_at');
    }


    
    public function entradas()
    {
        return $this->belongsToMany(Entrada::class, 'detalle_entradas', 'id_producto', 'id_entrada')
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
    }

    public function tipoProducto()
    {
        return $this->belongsTo(TipoProducto::class, 'id_tipo_producto');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    /** Etiqueta clave - nombre para selects generales de productos. */
    public function etiquetaClaveNombre(): string
    {
        $partes = array_filter([
            Insumo::normalizarCampoMostrar($this->clave),
            Insumo::normalizarCampoMostrar($this->nombre),
        ]);

        return implode(' - ', $partes);
    }

    private function syncInsumos(Producto $producto, $insumos)
    {
        $datosParaSync = [];

        foreach ($insumos ?? [] as $insumo) {
            $datosParaSync[$insumo['id']] = ['cantidad' => $insumo['cantidad']];
        }

        // Esto actualiza, elimina e inserta según sea necesario
        $producto->insumos()->sync($datosParaSync);
    }
}