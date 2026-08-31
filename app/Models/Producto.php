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

    /** Campos adicionales de medida (Medida, Ancho, Largo) segun las etiquetas del tipo de producto. */
    public function medidaMostrar(): string
    {
        $tipo = $this->tipoProducto;

        if (!$tipo) {
            return '';
        }

        $medidas = [];

        foreach ($tipo->camposPersonalizados() as $campo => $etiqueta) {
            if (!Insumo::etiquetaEsMedida(Insumo::normalizarCampoMostrar($etiqueta))) {
                continue;
            }

            $valor = Insumo::normalizarCampoMostrar($this->$campo);
            if ($valor !== '') {
                $medidas[] = $valor;
            }
        }

        return implode(' - ', $medidas);
    }

    /** Etiqueta clave - nombre - descripcion - color - medida para selects de productos en cotizaciones. */
    public function etiquetaCotizacion(): string
    {
        $partes = array_filter([
            Insumo::normalizarCampoMostrar($this->clave),
            Insumo::normalizarCampoMostrar($this->nombre),
            Insumo::normalizarCampoMostrar($this->descripcion),
            Insumo::normalizarCampoMostrar($this->color),
            $this->medidaMostrar(),
        ]);

        return implode(' - ', $partes);
    }

    /** Etiqueta nombre - descripcion - medida (campo1) - color para selects de cortinero. */
    public function etiquetaCortinero(): string
    {
        $partes = array_filter([
            Insumo::normalizarCampoMostrar($this->nombre),
            Insumo::normalizarCampoMostrar($this->descripcion),
            Insumo::normalizarCampoMostrar($this->campo1),
            Insumo::normalizarCampoMostrar($this->color),
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