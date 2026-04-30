<?php

namespace Tests\Feature;

use App\Models\DatoFuente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class IndicadoresIntegrationTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_usuario_con_permiso_puede_crear_indicador_version_y_variable(): void
    {
        $this->actingAsUserWithPermissions(['indicadores.configurar']);

        $datoFuente = $this->createDatoFuenteFixture();

        $indicadorResponse = $this->postJson('/api/indicadores', [
            'codigo_interno' => 'IND-TEST-001',
            'categoria_id' => 1,
            'nombre' => 'Indicador de prueba',
            'descripcion' => 'Indicador creado desde test de integracion',
            'publicable' => true,
            'sensible' => false,
            'activo' => true,
            'orden' => 100,
            'normas' => [
                [
                    'norma_id' => 1,
                    'codigo_en_norma' => '5.1',
                    'nombre_en_norma' => 'Indicador de prueba en norma',
                ],
            ],
        ]);

        $indicadorResponse
            ->assertCreated()
            ->assertJsonPath('codigo_interno', 'IND-TEST-001')
            ->assertJsonPath('normas.0.id', 1);

        $indicadorId = $indicadorResponse->json('id');

        $versionResponse = $this->postJson("/api/indicadores/{$indicadorId}/versiones", [
            'tipo_indicador_id' => 1,
            'unidad_medida_id' => 2,
            'periodicidad_id' => 1,
            'version' => '2026.1',
            'formula_tipo' => 'RATIO_CONSTANTE',
            'constante' => 100,
            'formula_texto' => 'A / B * 100',
            'formula_expression' => '(A/B)*100',
            'objetivo' => 'Medir un porcentaje',
            'observaciones_metodologicas' => 'Version creada en test',
            'vigente_desde' => '2026-01-01',
            'vigente_hasta' => '2026-12-31',
            'activa' => true,
        ]);

        $versionResponse
            ->assertCreated()
            ->assertJsonPath('version', '2026.1')
            ->assertJsonPath('formula_tipo', 'RATIO_CONSTANTE');

        $versionId = $versionResponse->json('id');

        $variableResponse = $this->postJson("/api/indicadores/{$indicadorId}/versiones/{$versionId}/variables", [
            'dato_fuente_id' => $datoFuente->id,
            'codigo_variable' => 'A',
            'rol' => 'NUMERADOR',
            'obligatorio' => true,
            'orden' => 1,
            'descripcion' => 'Variable principal',
        ]);

        $variableResponse
            ->assertCreated()
            ->assertJsonPath('codigo_variable', 'A')
            ->assertJsonPath('rol', 'NUMERADOR')
            ->assertJsonPath('dato_fuente.id', $datoFuente->id);
    }

    public function test_no_permite_versiones_activas_superpuestas_para_un_mismo_indicador(): void
    {
        $this->actingAsUserWithPermissions(['indicadores.configurar']);

        $indicadorResponse = $this->postJson('/api/indicadores', [
            'codigo_interno' => 'IND-TEST-OVERLAP',
            'categoria_id' => 1,
            'nombre' => 'Indicador con overlap',
            'descripcion' => 'Sirve para probar superposicion',
            'publicable' => true,
            'sensible' => false,
            'activo' => true,
            'orden' => 101,
        ])->assertCreated();

        $indicadorId = $indicadorResponse->json('id');

        $this->postJson("/api/indicadores/{$indicadorId}/versiones", [
            'tipo_indicador_id' => 1,
            'unidad_medida_id' => 2,
            'periodicidad_id' => 1,
            'version' => '2026.1',
            'formula_tipo' => 'RATIO_CONSTANTE',
            'constante' => 100,
            'formula_texto' => 'A / B * 100',
            'vigente_desde' => '2026-01-01',
            'vigente_hasta' => '2026-12-31',
            'activa' => true,
        ])->assertCreated();

        $this->postJson("/api/indicadores/{$indicadorId}/versiones", [
            'tipo_indicador_id' => 1,
            'unidad_medida_id' => 2,
            'periodicidad_id' => 1,
            'version' => '2026.2',
            'formula_tipo' => 'RATIO_CONSTANTE',
            'constante' => 100,
            'formula_texto' => 'A / B * 100',
            'vigente_desde' => '2026-06-01',
            'vigente_hasta' => '2027-05-31',
            'activa' => true,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['vigente_desde']);
    }

    private function createDatoFuenteFixture(): DatoFuente
    {
        return DatoFuente::create([
            'codigo_interno' => 'DF-TEST-001',
            'area_municipal_id' => 1,
            'unidad_medida_id' => 1,
            'periodicidad_id' => 1,
            'modalidad_carga_id' => 1,
            'fuente_institucional_id' => null,
            'responsable_usuario_id' => null,
            'nombre' => 'Dato fuente de prueba',
            'descripcion' => 'Dato fuente auxiliar para tests',
            'tipo_dato' => 'entero',
            'metodo_obtencion' => 'manual',
            'link_fuente' => null,
            'rango_minimo' => 0,
            'rango_maximo' => 999999,
            'nivel_geografico' => 'Municipio',
            'activo' => true,
        ]);
    }
}
