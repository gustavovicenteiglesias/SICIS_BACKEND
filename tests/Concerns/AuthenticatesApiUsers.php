<?php

namespace Tests\Concerns;

use App\Models\AreaMunicipal;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Usuario;
use Database\Seeders\DatabaseSeeder;
use Laravel\Sanctum\Sanctum;

trait AuthenticatesApiUsers
{
    protected function seedBaseData(): void
    {
        $this->seed(DatabaseSeeder::class);
    }

    protected function actingAsAdmin(): Usuario
    {
        $this->seedBaseData();

        $usuario = Usuario::query()
            ->where('nombre_usuario', 'admin')
            ->firstOrFail();

        Sanctum::actingAs($usuario);

        return $usuario;
    }

    protected function actingAsUserWithoutPermissions(array $attributes = []): Usuario
    {
        $usuario = $this->createApiUser($attributes);

        Sanctum::actingAs($usuario);

        return $usuario;
    }

    protected function actingAsUserWithPermissions(array $permissionCodes, array $attributes = []): Usuario
    {
        $usuario = $this->createApiUserWithPermissions($permissionCodes, $attributes);

        Sanctum::actingAs($usuario);

        return $usuario;
    }

    protected function createApiUser(array $attributes = [], array $roleCodes = []): Usuario
    {
        $this->seedBaseData();

        $areaId = AreaMunicipal::query()->value('id');

        $usuario = Usuario::create(array_merge([
            'area_municipal_id' => $areaId,
            'nombre_usuario' => 'usuario_'.str()->lower(str()->random(10)),
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'email' => str()->lower(str()->random(10)).'@example.test',
            'password' => '12345678',
            'activo' => true,
        ], $attributes));

        if ($roleCodes !== []) {
            $roles = Rol::query()
                ->whereIn('codigo', $roleCodes)
                ->pluck('id')
                ->all();

            $usuario->roles()->sync($roles);
        }

        return $usuario;
    }

    protected function createApiUserWithPermissions(array $permissionCodes, array $attributes = []): Usuario
    {
        $usuario = $this->createApiUser($attributes);

        $permissionIds = Permiso::query()
            ->whereIn('codigo', $permissionCodes)
            ->pluck('id')
            ->all();

        $role = Rol::create([
            'codigo' => 'TEST_'.str()->upper(str()->random(12)),
            'nombre' => 'Rol de prueba '.str()->random(6),
            'descripcion' => 'Rol temporal para pruebas automatizadas',
            'activo' => true,
        ]);

        $role->permisos()->sync($permissionIds);
        $usuario->roles()->sync([$role->id]);

        return $usuario->fresh('roles.permisos');
    }
}
