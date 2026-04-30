<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class ErrorHandlingConsistencyTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_401_devuelve_estructura_estandar(): void
    {
        $response = $this->getJson('/api/catalogos/categorias');

        $response
            ->assertUnauthorized()
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('ok', false)
            ->assertJsonPath('code', 'AUTH_REQUIRED')
            ->assertJsonPath('path', 'api/catalogos/categorias');
    }

    public function test_403_devuelve_estructura_estandar(): void
    {
        $this->actingAsUserWithoutPermissions();

        $response = $this->postJson('/api/catalogos/categorias', [
            'nombre' => 'Categoria sin permiso',
            'descripcion' => 'No deberia poder crearla',
            'orden' => 99,
            'activa' => true,
        ]);

        $response
            ->assertForbidden()
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('ok', false)
            ->assertJsonPath('code', 'FORBIDDEN');
    }

    public function test_422_devuelve_estructura_estandar(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/catalogos/categorias', [
            'descripcion' => 'Falta el nombre requerido',
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('X-Request-Id')
            ->assertJsonPath('ok', false)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'nombre',
                ],
                'request_id',
                'path',
            ]);
    }
}
