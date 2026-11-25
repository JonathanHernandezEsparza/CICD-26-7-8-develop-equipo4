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

    // === SECCION INTEGRANTE 3 (PABLO - 23150304) ===

    public function test_beca_se_pierde_con_promedio_84_99_y_se_conserva_con_85(): void
    {
        $this->assertFalse($this->controller->evaluarConservacionBeca(true, 84.99, 0, 95.0));
        $this->assertTrue($this->controller->evaluarConservacionBeca(true, 85.0, 0, 95.0));
    }

    public function test_beca_se_pierde_con_asistencia_89_9_y_se_conserva_con_90(): void
    {
        $this->assertFalse($this->controller->evaluarConservacionBeca(true, 92.0, 0, 89.9));
        $this->assertTrue($this->controller->evaluarConservacionBeca(true, 92.0, 0, 90.0));
    }

    public function test_bloqueo_por_asistencia_79_9_devuelve_mensaje_y_con_80_no_bloquea(): void
    {
        $this->assertSame(
            'La asistencia es menor al 80% requerido.',
            $this->controller->verificarBloqueos(false, 79.9, 0)
        );
        $this->assertNull($this->controller->verificarBloqueos(false, 80.0, 0));
    }

    // === SECCION INTEGRANTE 4 ===

    // === SECCION INTEGRANTE 5 ===

    // === SECCION INTEGRANTE 6 ===
}
