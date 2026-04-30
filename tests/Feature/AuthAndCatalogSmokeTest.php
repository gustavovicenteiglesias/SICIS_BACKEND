<?php

namespace Tests\Feature;

use App\Models\Categoria;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesApiUsers;
use Tests\TestCase;

class AuthAndCatalogSmokeTest extends TestCase
{
    use AuthenticatesApiUsers;
    use RefreshDatabase;

    public function test_login_exitoso(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->postJson('/api/login', [
            'usuario' => 'admin',
            'password' => '12345678',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('mensaje', 'Login exitoso')
            ->assertJsonPath('usuario.nombre_usuario', 'admin')
            ->assertJsonPath('usuario.activo', true)
            ->assertJsonStructure([
                'token',
                'usuario' => [
                    'id',
                    'nombre_usuario',
                    'nombre',
                    'apellido',
                    'email',
                    'activo',
                    'roles' => [
                        '*' => ['id', 'codigo', 'nombre'],
                    ],
                ],
            ]);
    }

    public function test_acceso_protegido_sin_token_devuelve_401(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->getJson('/api/catalogos/categorias')
            ->assertUnauthorized();
    }

    public function test_permiso_insuficiente_devuelve_403(): void
    {
        $this->actingAsUserWithoutPermissions();

        $this->postJson('/api/catalogos/categorias', [
            'nombre' => 'Categoria sin permiso',
            'descripcion' => 'No deberia poder crearla',
            'orden' => 99,
            'activa' => true,
        ])->assertForbidden();
    }

    public function test_listado_de_categorias_para_usuario_autorizado(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/catalogos/categorias');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);

        $this->assertNotEmpty($response->json('data'));
        $this->assertSame('Economia', $response->json('data.0.nombre'));
    }

    public function test_alta_de_categoria_con_usuario_autorizado(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/catalogos/categorias', [
            'nombre' => 'Tecnologia Civica',
            'descripcion' => 'Indicadores de innovacion y transformacion digital',
            'orden' => 50,
            'activa' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('nombre', 'Tecnologia Civica')
            ->assertJsonPath('orden', 50)
            ->assertJsonPath('activa', true);

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Tecnologia Civica',
            'orden' => 50,
        ]);

        $this->assertSame(10, Categoria::query()->count());
    }
}
