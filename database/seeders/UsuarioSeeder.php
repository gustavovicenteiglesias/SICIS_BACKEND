<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $areaId = DB::table('areas_municipales')->insertGetId([
            'nombre' => 'Direccion de Modernizacion',
            'descripcion' => 'Area responsable de la carga operativa inicial',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $usuarioId = DB::table('usuarios')->insertGetId([
            'area_municipal_id' => $areaId,
            'nombre_usuario' => 'admin',
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'email' => 'admin@lujan.gob.ar',
            'password' => Hash::make('12345678'),
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('usuarios_roles')->insert([
            'usuario_id' => $usuarioId,
            'rol_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
