<?php

namespace Tests\Unit;

use App\Models\Producto;
use App\Models\TipoProducto;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductoTipoProductoTest extends TestCase
{
    #[Test]
    public function producto_can_define_a_tipo_producto_relationship()
    {
        $this->assertTrue(method_exists(Producto::class, 'tipoProducto'));
        $this->assertTrue(method_exists(TipoProducto::class, 'productos'));
    }
}
