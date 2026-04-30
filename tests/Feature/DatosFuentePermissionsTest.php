<?php

namespace Tests\Feature;

use App\Models\DatoFuente;
use App\Models\DatoFuenteValor;
use App\Models\EstadoDato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class DatosFuentePermissionsTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_usuario_con_permiso_de_configuracion_puede_crear_dato_fuente(): void
    {
        $usuario = $this->actingAsUserWithPermissions(['datos_fuente.configurar']);

        $response = $this->postJson('/api/datos-fuente', [
            'codigo_interno' => 'DF-CONF-001',
            'area_municipal_id' => 1,
            'unidad_medida_id' => 1,
            'periodicidad_id' => 1,
            'modalidad_carga_id' => 1,
            'responsable_usuario_id' => $usuario->id,
            'nombre' => 'Dato fuente configurable',
            'descripcion' => 'Creado desde test',
            'tipo_dato' => 'decimal',
            'metodo_obtencion' => 'manual',
            'rango_minimo' => 0,
            'rango_maximo' => 1000,
            'nivel_geografico' => 'Municipio',
            'activo' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('codigo_interno', 'DF-CONF-001')
            ->assertJsonPath('responsable_usuario.id', $usuario->id);
    }

    public function test_usuario_con_permiso_de_carga_puede_cargar_valor_pero_no_validarlo(): void
    {
        $cargador = $this->actingAsUserWithPermissions(['datos_fuente.cargar']);
        $datoFuente = $this->createDatoFuenteFixture();

        $response = $this->postJson("/api/datos-fuente/{$datoFuente->id}/valores", [
            'jurisdiccion_id' => 3,
            'estado_dato_id' => 2,
            'modalidad_carga_id' => 1,
            'valor_crudo' => 150,
            'valor_utilizado' => null,
            'periodo_referencia' => '2026-01-01',
            'fecha_produccion' => '2026-01-15',
            'vigente' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('usuario_carga.id', $cargador->id)
            ->assertJsonPath('estado_dato.codigo', 'CARGADO');

        $valorId = $response->json('id');

        $this->postJson("/api/datos-fuente/{$datoFuente->id}/valores/{$valorId}/validar", [
            'estado_dato_id' => 4,
            'valor_utilizado' => 140,
            'vigente' => true,
        ])->assertForbidden();
    }

    public function test_usuario_con_permiso_de_validacion_puede_validar_valor_existente(): void
    {
        $datoFuente = $this->createDatoFuenteFixture();
        $valor = $this->createDatoFuenteValorFixture($datoFuente);

        $validador = $this->actingAsUserWithPermissions(['datos_fuente.validar']);

        $response = $this->postJson("/api/datos-fuente/{$datoFuente->id}/valores/{$valor->id}/validar", [
            'estado_dato_id' => 4,
            'valor_utilizado' => 95,
            'vigente' => true,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('estado_dato.codigo', 'VALIDADO')
            ->assertJsonPath('usuario_valida.id', $validador->id)
            ->assertJsonPath('valor_utilizado', '95.000000');

        $this->assertDatabaseHas('datos_fuente_valores', [
            'id' => $valor->id,
            'estado_dato_id' => 4,
            'usuario_valida_id' => $validador->id,
        ]);
    }

    private function createDatoFuenteFixture(): DatoFuente
    {
        $this->seedBaseData();

        return DatoFuente::create([
            'codigo_interno' => 'DF-TEST-'.str()->upper(str()->random(8)),
            'area_municipal_id' => 1,
            'unidad_medida_id' => 1,
            'periodicidad_id' => 1,
            'modalidad_carga_id' => 1,
            'fuente_institucional_id' => null,
            'responsable_usuario_id' => null,
            'nombre' => 'Dato fuente test '.str()->random(5),
            'descripcion' => 'Dato fuente auxiliar',
            'tipo_dato' => 'decimal',
            'metodo_obtencion' => 'manual',
            'link_fuente' => null,
            'rango_minimo' => 0,
            'rango_maximo' => 1000,
            'nivel_geografico' => 'Municipio',
            'activo' => true,
        ]);
    }

    private function createDatoFuenteValorFixture(DatoFuente $datoFuente): DatoFuenteValor
    {
        $usuario = $this->createApiUser();
        $estadoCargado = EstadoDato::query()->where('codigo', 'CARGADO')->firstOrFail();

        return DatoFuenteValor::create([
            'dato_fuente_id' => $datoFuente->id,
            'jurisdiccion_id' => 3,
            'estado_dato_id' => $estadoCargado->id,
            'modalidad_carga_id' => 1,
            'usuario_carga_id' => $usuario->id,
            'valor_crudo' => 100,
            'valor_utilizado' => null,
            'periodo_referencia' => '2026-01-01',
            'fecha_produccion' => '2026-01-15',
            'fecha_carga' => now(),
            'observado_motivo' => null,
            'vigente' => true,
        ]);
    }
}
