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

    public function test_carga_maxima_regular_devuelve_36(): void
    {
        $this->assertSame(36, $this->controller->calcularCargaMaxima(85.0, 0));
    }

    // === SECCION INTEGRANTE 2 ===

    // === SECCION INTEGRANTE 3 ===

    // === SECCION INTEGRANTE 4 ===

    // === SECCION INTEGRANTE 5 ===

    public function test_carga_maxima_con_promedio_cero_devuelve_36(): void
    {
        $this->assertSame(36, $this->controller->calcularCargaMaxima(0.0, 0));
    }

    public function test_cuota_semestral_con_15_reprobadas_devuelve_10250(): void
    {
        $this->assertSame(10250.0, $this->controller->calcularCuotaSemestral(false, 15));
    }

    // === SECCION INTEGRANTE 6 ===
}
