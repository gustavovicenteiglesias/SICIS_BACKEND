<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SicisCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('permisos')->upsert([
            ['id' => 1, 'codigo' => 'usuarios.gestionar', 'nombre' => 'Gestionar usuarios', 'descripcion' => 'Alta, baja y modificacion de usuarios', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'roles.gestionar', 'nombre' => 'Gestionar roles', 'descripcion' => 'Administrar roles y permisos', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'indicadores.ver', 'nombre' => 'Ver indicadores', 'descripcion' => 'Consultar indicadores', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'indicadores.configurar', 'nombre' => 'Configurar indicadores', 'descripcion' => 'Configurar visualizacion, version y metodologia', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'codigo' => 'datos.cargar', 'nombre' => 'Cargar datos', 'descripcion' => 'Permiso legado de carga de datos fuente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'codigo' => 'datos.validar', 'nombre' => 'Validar datos', 'descripcion' => 'Permiso legado de validacion de datos fuente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'codigo' => 'corridas.ejecutar', 'nombre' => 'Ejecutar corridas', 'descripcion' => 'Ejecutar corridas de calculo', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'codigo' => 'corridas.aprobar', 'nombre' => 'Aprobar corridas', 'descripcion' => 'Aprobar u observar corridas', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'codigo' => 'resultados.publicar', 'nombre' => 'Publicar resultados', 'descripcion' => 'Publicar resultados validados', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'codigo' => 'auditoria.ver', 'nombre' => 'Ver auditoria', 'descripcion' => 'Consultar auditoria del sistema', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'codigo' => 'datos_fuente.ver', 'nombre' => 'Ver datos fuente', 'descripcion' => 'Consultar catalogo y valores de datos fuente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'codigo' => 'datos_fuente.configurar', 'nombre' => 'Configurar datos fuente', 'descripcion' => 'Administrar catalogo de datos fuente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'codigo' => 'datos_fuente.cargar', 'nombre' => 'Cargar datos fuente', 'descripcion' => 'Cargar valores y evidencias de datos fuente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'codigo' => 'datos_fuente.validar', 'nombre' => 'Validar datos fuente', 'descripcion' => 'Validar, observar o cerrar valores de datos fuente', 'created_at' => $now, 'updated_at' => $now],
        ], ['codigo'], ['nombre', 'descripcion', 'updated_at']);

        DB::table('areas_municipales')->upsert([
            ['id' => 1, 'nombre' => 'Direccion de Modernizacion', 'descripcion' => 'Area responsable de la carga operativa inicial', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nombre' => 'Hacienda', 'descripcion' => 'Area municipal', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nombre' => 'Salud', 'descripcion' => 'Area municipal', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nombre' => 'Obras y Servicios Publicos', 'descripcion' => 'Area municipal', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nombre' => 'Ambiente', 'descripcion' => 'Area municipal', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['nombre', 'descripcion', 'activa', 'updated_at']);

        DB::table('normas')->upsert([
            ['id' => 1, 'codigo' => 'IRAM_ISO_37120', 'nombre' => 'Ciudades y comunidades sostenibles - Indicadores de servicios urbanos y calidad de vida', 'version' => '2018', 'anio' => 2018, 'descripcion' => 'Adaptacion local IRAM-ISO 37120', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'ISO_37120', 'nombre' => 'Ciudades y comunidades sostenibles - Indicadores de servicios urbanos y calidad de vida', 'version' => '2018', 'anio' => 2018, 'descripcion' => 'Norma internacional base', 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'version', 'anio', 'descripcion', 'activa', 'updated_at']);

        DB::table('tipos_indicador')->upsert([
            ['id' => 1, 'codigo' => 'CORE', 'nombre' => 'Principal', 'descripcion' => 'Indicador principal de la metodologia', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'SUPPORTING', 'nombre' => 'Apoyo', 'descripcion' => 'Indicador complementario', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'PROFILE', 'nombre' => 'Perfil', 'descripcion' => 'Dato de contexto o perfil', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'LOCAL', 'nombre' => 'Local', 'descripcion' => 'Indicador municipal propio', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('periodicidades')->upsert([
            ['id' => 1, 'codigo' => 'ANUAL', 'nombre' => 'Anual', 'descripcion' => 'Una medicion por anio', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'SEMESTRAL', 'nombre' => 'Semestral', 'descripcion' => 'Una medicion por semestre', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'TRIMESTRAL', 'nombre' => 'Trimestral', 'descripcion' => 'Una medicion por trimestre', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'MENSUAL', 'nombre' => 'Mensual', 'descripcion' => 'Una medicion por mes', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'codigo' => 'DIARIA', 'nombre' => 'Diaria', 'descripcion' => 'Una medicion por dia', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('unidades_medida')->upsert([
            ['id' => 1, 'nombre' => 'cantidad', 'simbolo' => 'u', 'descripcion' => 'Conteo absoluto', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nombre' => 'porcentaje', 'simbolo' => '%', 'descripcion' => 'Relacion porcentual', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nombre' => 'habitantes', 'simbolo' => 'hab', 'descripcion' => 'Cantidad de habitantes', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nombre' => 'kilometros cuadrados', 'simbolo' => 'km2', 'descripcion' => 'Superficie en kilometros cuadrados', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nombre' => 'habitantes por kilometro cuadrado', 'simbolo' => 'hab/km2', 'descripcion' => 'Densidad poblacional', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nombre' => 'milimetros', 'simbolo' => 'mm', 'descripcion' => 'Milimetros', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nombre' => 'por cada 100.000 habitantes', 'simbolo' => '/100.000 hab', 'descripcion' => 'Tasa cada cien mil habitantes', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'nombre' => 'pesos argentinos', 'simbolo' => 'ARS', 'descripcion' => 'Moneda local', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['nombre', 'simbolo', 'descripcion', 'updated_at']);

        DB::table('estados_dato')->upsert([
            ['id' => 1, 'codigo' => 'BORRADOR', 'nombre' => 'Borrador', 'descripcion' => 'Dato iniciado, no enviado a revision', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'CARGADO', 'nombre' => 'Cargado', 'descripcion' => 'Dato cargado y pendiente de validacion', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'OBSERVADO', 'nombre' => 'Observado', 'descripcion' => 'Dato observado por inconsistencia', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'VALIDADO', 'nombre' => 'Validado', 'descripcion' => 'Dato validado para corridas', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'codigo' => 'RECHAZADO', 'nombre' => 'Rechazado', 'descripcion' => 'Dato rechazado', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('estados_corrida')->upsert([
            ['id' => 1, 'codigo' => 'BORRADOR', 'nombre' => 'Borrador', 'descripcion' => 'Corrida creada pero no ejecutada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'EJECUTADA', 'nombre' => 'Ejecutada', 'descripcion' => 'Corrida calculada pendiente de aprobacion', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'OBSERVADA', 'nombre' => 'Observada', 'descripcion' => 'Corrida observada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'APROBADA', 'nombre' => 'Aprobada', 'descripcion' => 'Corrida aprobada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'codigo' => 'PUBLICADA', 'nombre' => 'Publicada', 'descripcion' => 'Corrida aprobada y publicada', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('estados_resultado')->upsert([
            ['id' => 1, 'codigo' => 'CALCULADO', 'nombre' => 'Calculado', 'descripcion' => 'Resultado calculado correctamente', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'OBSERVADO', 'nombre' => 'Observado', 'descripcion' => 'Resultado observado', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'SIN_DATOS', 'nombre' => 'Sin datos', 'descripcion' => 'Faltan datos fuente validados', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'ERROR_CALCULO', 'nombre' => 'Error de calculo', 'descripcion' => 'Error durante el calculo', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('modalidades_carga')->upsert([
            ['id' => 1, 'codigo' => 'MANUAL', 'nombre' => 'Manual', 'descripcion' => 'Carga manual por usuario', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'API', 'nombre' => 'API', 'descripcion' => 'Carga automatica desde API', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'ARCHIVO', 'nombre' => 'Archivo', 'descripcion' => 'Carga por archivo', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('tipos_jurisdiccion')->upsert([
            ['id' => 1, 'codigo' => 'PAIS', 'nombre' => 'Pais', 'descripcion' => 'Pais', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'codigo' => 'PROVINCIA', 'nombre' => 'Provincia', 'descripcion' => 'Provincia', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'codigo' => 'MUNICIPIO', 'nombre' => 'Municipio', 'descripcion' => 'Municipio', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'codigo' => 'LOCALIDAD', 'nombre' => 'Localidad', 'descripcion' => 'Localidad', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'codigo' => 'BARRIO', 'nombre' => 'Barrio', 'descripcion' => 'Barrio', 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['codigo', 'nombre', 'descripcion', 'updated_at']);

        DB::table('jurisdicciones')->upsert([
            ['id' => 1, 'tipo_jurisdiccion_id' => 1, 'jurisdiccion_padre_id' => null, 'nombre' => 'Argentina', 'codigo_oficial' => null, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'tipo_jurisdiccion_id' => 2, 'jurisdiccion_padre_id' => 1, 'nombre' => 'Buenos Aires', 'codigo_oficial' => null, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'tipo_jurisdiccion_id' => 3, 'jurisdiccion_padre_id' => 2, 'nombre' => 'Lujan', 'codigo_oficial' => null, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['tipo_jurisdiccion_id', 'jurisdiccion_padre_id', 'nombre', 'codigo_oficial', 'activa', 'updated_at']);

        DB::table('categorias')->upsert([
            ['id' => 1, 'nombre' => 'Economia', 'descripcion' => 'Indicadores economicos', 'orden' => 1, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nombre' => 'Educacion', 'descripcion' => 'Indicadores educativos', 'orden' => 2, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nombre' => 'Energia', 'descripcion' => 'Indicadores de energia', 'orden' => 3, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nombre' => 'Ambiente', 'descripcion' => 'Indicadores ambientales', 'orden' => 4, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nombre' => 'Salud', 'descripcion' => 'Indicadores sanitarios', 'orden' => 5, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nombre' => 'Vivienda', 'descripcion' => 'Indicadores de vivienda', 'orden' => 6, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'nombre' => 'Movilidad', 'descripcion' => 'Indicadores de movilidad y transporte', 'orden' => 7, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'nombre' => 'Gobernanza', 'descripcion' => 'Indicadores de gestion y gobierno', 'orden' => 8, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'nombre' => 'Poblacion y territorio', 'descripcion' => 'Indicadores de poblacion y territorio', 'orden' => 9, 'activa' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['id'], ['nombre', 'descripcion', 'orden', 'activa', 'updated_at']);

        DB::table('roles_permisos')->upsert([
            ['rol_id' => 1, 'permiso_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 9, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 11, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 13, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 1, 'permiso_id' => 14, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 2, 'permiso_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 2, 'permiso_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 3, 'permiso_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 4, 'permiso_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['rol_id' => 5, 'permiso_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ], ['rol_id', 'permiso_id'], ['updated_at']);
    }
}
