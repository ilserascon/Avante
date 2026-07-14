<?php

namespace Tests\Unit;

use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoPrecioTest extends TestCase
{
    use RefreshDatabase;

    public function test_producto_can_store_precio_during_creation()
    {
        $producto = Producto::create([
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Descripción de prueba',
            'precio' => 125.50,
        ]);

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'precio' => '125.50',
        ]);
    }
}
