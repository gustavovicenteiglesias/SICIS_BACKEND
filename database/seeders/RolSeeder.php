<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'codigo' => 'ADMINISTRADOR_GENERAL', 'nombre' => 'Administrador general', 'descripcion' => 'Gestion integral del sistema'],
            ['id' => 2, 'codigo' => 'GESTOR_INDICADORES', 'nombre' => 'Gestor de indicadores', 'descripcion' => 'Administra catalogo metodologico y configuracion'],
            ['id' => 3, 'codigo' => 'CARGADOR_DATOS', 'nombre' => 'Cargador de datos', 'descripcion' => 'Carga datos fuente manuales o revisa datos importados'],
            ['id' => 4, 'codigo' => 'DECISOR', 'nombre' => 'Decisor / aprobador', 'descripcion' => 'Valida, rechaza, aprueba corridas y decide publicacion'],
            ['id' => 5, 'codigo' => 'AUDITOR_INTERNO', 'nombre' => 'Auditor / consulta interna', 'descripcion' => 'Consulta interna y auditoria'],
            ['id' => 6, 'codigo' => 'USUARIO_PUBLICO', 'nombre' => 'Usuario publico', 'descripcion' => 'Consulta publica sin acceso al backoffice'],
        ]);
    }
}
