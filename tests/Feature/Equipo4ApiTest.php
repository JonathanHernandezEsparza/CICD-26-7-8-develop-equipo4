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

    public function test_alumno_regular_con_32_creditos_es_autorizado(): void
    {
        $response = $this->postJson($this->endpoint, [
            'promedio_anterior' => 82,
            'materias_reprobadas' => 0,
            'creditos_acumulados' => 120,
            'creditos_solicitados' => 32,
            'asistencia_porcentaje' => 92,
            'tiene_beca' => false,
            'tiene_adeudo' => false,
            'ingles_aprobado' => false,
        ]);

        $response->assertOk()
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
            ])
            ->assertJson([
                'autorizado' => true,
                'detalle_academico' => [
                    'carga_maxima_permitida' => 36,
                    'creditos_solicitados' => 32,
                    'conserva_beca' => false,
                    'cuota_semestral' => 3500.0,
                    'servicio_social_disponible' => false,
                    'residencias_disponibles' => false,
                ],
            ]);
    }

    // === SECCION INTEGRANTE 2 ===

    // === SECCION INTEGRANTE 3 (PABLO - 23150304) ===

    public function test_frontera_beca_con_promedio_84_99_pierde_la_beca_y_paga_cuota_completa(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'promedio_anterior' => 84.99,
            'tiene_beca' => true,
            'asistencia_porcentaje' => 95.0,
            'creditos_solicitados' => 30,
        ]))
            ->assertOk()
            ->assertJsonPath('autorizado', true)
            ->assertJsonPath('detalle_academico.conserva_beca', false)
            ->assertJsonPath('detalle_academico.cuota_semestral', 3500.0)
            ->assertJsonPath('detalle_academico.carga_maxima_permitida', 36);
    }

    public function test_frontera_beca_con_promedio_85_conserva_la_beca_y_paga_media_cuota(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'promedio_anterior' => 85.0,
            'tiene_beca' => true,
            'asistencia_porcentaje' => 95.0,
            'creditos_solicitados' => 30,
        ]))
            ->assertOk()
            ->assertJsonPath('autorizado', true)
            ->assertJsonPath('detalle_academico.conserva_beca', true)
            ->assertJsonPath('detalle_academico.cuota_semestral', 1750.0)
            ->assertJsonPath('detalle_academico.carga_maxima_permitida', 36);
    }

    public function test_frontera_carga_alta_con_promedio_89_99_mantiene_carga_36_y_rechaza_42_creditos(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'promedio_anterior' => 89.99,
            'tiene_beca' => true,
            'asistencia_porcentaje' => 95.0,
            'creditos_solicitados' => 42,
        ]))
            ->assertOk()
            ->assertJsonPath('autorizado', false)
            ->assertJsonPath('detalle_academico.carga_maxima_permitida', 36)
            ->assertJsonPath('detalle_academico.cuota_semestral', 0.0);
    }

    public function test_frontera_carga_alta_con_promedio_90_sube_la_carga_a_42_y_autoriza_42_creditos(): void
    {
        $this->postJson($this->endpoint, $this->payloadValido([
            'promedio_anterior' => 90.0,
            'tiene_beca' => true,
            'asistencia_porcentaje' => 95.0,
            'creditos_solicitados' => 42,
        ]))
            ->assertOk()
            ->assertJsonPath('autorizado', true)
            ->assertJsonPath('detalle_academico.carga_maxima_permitida', 42)
            ->assertJsonPath('detalle_academico.cuota_semestral', 1750.0);
    }

    // === SECCION INTEGRANTE 4 ===

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
