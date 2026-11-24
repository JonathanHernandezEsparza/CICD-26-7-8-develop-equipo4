<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\Controllers\Equipo4Controller;
use PHPUnit\Framework\TestCase;

class Equipo4LogicaTest extends TestCase
{
    private Equipo4Controller $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new Equipo4Controller;
    }

    public function test_el_controlador_se_inicializa_correctamente(): void
    {
        $this->assertInstanceOf(Equipo4Controller::class, $this->controller);
    }

    // === SECCION INTEGRANTE 1 (LIDER - 23150306) ===

    // === SECCION INTEGRANTE 2 ===

    // === SECCION INTEGRANTE 3 ===

    // === SECCION INTEGRANTE 4 ===

    // === SECCION INTEGRANTE 5 ===

    // === SECCION INTEGRANTE 6 ===
}
