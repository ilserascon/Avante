<?php

namespace Tests\Unit;

use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CotizacionCortinaRelationshipTest extends TestCase
{
    #[Test]
    public function cotizacion_can_have_one_detalle_cotizacion()
    {
        $this->assertTrue(method_exists(Cotizacion::class, 'detalleCotizacion'));
        $this->assertTrue(method_exists(DetalleCotizacion::class, 'cotizacion'));
    }
}
