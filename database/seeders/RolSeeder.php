<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->upsert([
            ['id' => 1, 'codigo' => 'ADMINISTRADOR_GENERAL', 'nombre' => 'Administrador general', 'descripcion' => 'Gestion integral del sistema', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'GESTOR_INDICADORES', 'nombre' => 'Gestor de indicadores', 'descripcion' => 'Administra catalogo metodologico y configuracion', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'CARGADOR_DATOS', 'nombre' => 'Cargador de datos', 'descripcion' => 'Carga datos fuente manuales o revisa datos importados', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'DECISOR', 'nombre' => 'Decisor / aprobador', 'descripcion' => 'Valida, rechaza, aprueba corridas y decide publicacion', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'codigo' => 'AUDITOR_INTERNO', 'nombre' => 'Auditor / consulta interna', 'descripcion' => 'Consulta interna y auditoria', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'codigo' => 'USUARIO_PUBLICO', 'nombre' => 'Usuario publico', 'descripcion' => 'Consulta publica sin acceso al backoffice', 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'activo', 'updated_at']);
    }
}
