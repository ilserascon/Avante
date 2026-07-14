<?php

namespace Tests\Feature;

use Tests\TestCase;

class TipoProductoModuleTest extends TestCase
{
    public function test_tipo_productos_index_route_is_available()
    {
        $response = $this->get(route('admin.tipo-productos.index'));

        $response->assertRedirect('/login');
    }
}
