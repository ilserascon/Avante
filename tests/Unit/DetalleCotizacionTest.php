<?php

namespace Tests\Unit;

use App\Models\DetalleCotizacion;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DetalleCotizacionTest extends TestCase
{
    #[Test]
    public function it_allows_mass_assigning_the_new_detail_flags()
    {
        $detalle = new DetalleCotizacion();

        $detalle->fill([
            'lleva_cortina' => true,
            'lleva_tergal' => false,
            'lleva_forro' => true,
        ]);

        $this->assertTrue($detalle->lleva_cortina);
        $this->assertFalse($detalle->lleva_tergal);
        $this->assertTrue($detalle->lleva_forro);
    }
}
