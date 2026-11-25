<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class Equipo4ApiTest extends TestCase
{
    private string $endpoint = '/api/equipo-4/evaluar-reinscripcion';

    /**
     * Payload válido base que cada integrante puede reutilizar y sobrescribir.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payloadValido(array $overrides = []): array
    {
        return array_merge([
            'promedio_anterior' => 88.5,
            'materias_reprobadas' => 0,
            'creditos_acumulados' => 120,
            'creditos_solicitados' => 30,
            'asistencia_porcentaje' => 95.0,
            'tiene_beca' => false,
            'tiene_adeudo' => false,
            'ingles_aprobado' => false,
        ], $overrides);
    }

    public function test_el_endpoint_responde_con_la_estructura_esperada(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido())
            ->assertOk()
            ->assertJsonStructure([
                'autorizado',
                'motivo',
                'detalle_academico' => [
                    'carga_maxima_permitida',
                    'creditos_solicitados',
                    'conserva_beca',
                    'cuota_semestral',
                    'servicio_social_disponible',
                    'residencias_disponibles',
                ],
            ]);
    }

    // === SECCION INTEGRANTE 1 (LIDER - 23150306) ===

    // === SECCION INTEGRANTE 2 ===

    // === SECCION INTEGRANTE 3 ===

    // === SECCION INTEGRANTE 4 ===

    public function test_dos_reprobadas_autorizan_24_creditos_pero_rechazan_25(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'materias_reprobadas' => 2,
            'creditos_solicitados' => 24,
        ]))
            ->assertOk()
            ->assertJsonPath('autorizado', true)
            ->assertJsonPath('detalle_academico.carga_maxima_permitida', 24)
            ->assertJsonPath('detalle_academico.cuota_semestral', 4400.0);

        $this->postJson($this->endpoint, $this->payloadValido([
            'materias_reprobadas' => 2,
            'creditos_solicitados' => 25,
        ]))
            ->assertOk()
            ->assertJsonPath('autorizado', false)
            ->assertJsonPath('detalle_academico.carga_maxima_permitida', 24);
    }

    public function test_servicio_social_comienza_con_182_creditos_acumulados(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'creditos_acumulados' => 181,
        ]))
            ->assertOk()
            ->assertJsonPath('detalle_academico.servicio_social_disponible', false);

        $this->postJson($this->endpoint, $this->payloadValido([
            'creditos_acumulados' => 182,
        ]))
            ->assertOk()
            ->assertJsonPath('detalle_academico.servicio_social_disponible', true);
    }

    // === SECCION INTEGRANTE 5 ===

    public function test_payload_vacio_devuelve_422_con_error_en_los_8_campos(): void
    {
        $this->postJson($this->endpoint, [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'promedio_anterior',
                'materias_reprobadas',
                'creditos_acumulados',
                'creditos_solicitados',
                'asistencia_porcentaje',
                'tiene_beca',
                'tiene_adeudo',
                'ingles_aprobado',
            ]);
    }

    public function test_tipos_erroneos_devuelven_422(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'promedio_anterior' => 'noventa',
            'tiene_beca' => 'si',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['promedio_anterior', 'tiene_beca']);
    }

    public function test_valores_fuera_de_rango_devuelven_422(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'promedio_anterior' => 105,
            'creditos_solicitados' => 0,
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['promedio_anterior', 'creditos_solicitados']);
    }

    // === SECCION INTEGRANTE 6 ===
}
