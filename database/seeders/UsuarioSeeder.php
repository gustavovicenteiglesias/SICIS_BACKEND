<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $areaId = DB::table('areas_municipales')
            ->where('nombre', 'Direccion de Modernizacion')
            ->value('id');

        DB::table('usuarios')->updateOrInsert(
            ['nombre_usuario' => 'admin'],
            [
                'area_municipal_id' => $areaId,
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'admin@lujan.gob.ar',
                'password' => Hash::make('12345678'),
                'activo' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $usuarioId = DB::table('usuarios')
            ->where('nombre_usuario', 'admin')
            ->value('id');

        DB::table('usuarios_roles')->updateOrInsert(
            [
                'usuario_id' => $usuarioId,
                'rol_id' => 1,
            ],
            [
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
