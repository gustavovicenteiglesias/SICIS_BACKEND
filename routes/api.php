<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Catalogos\AreaMunicipalController;
use App\Http\Controllers\Catalogos\CategoriaController;
use App\Http\Controllers\Catalogos\CategoriaTematicaController;
use App\Http\Controllers\Catalogos\EstadoCorridaController;
use App\Http\Controllers\Catalogos\EstadoDatoController;
use App\Http\Controllers\Catalogos\EstadoResultadoController;
use App\Http\Controllers\Catalogos\FuenteInstitucionalController;
use App\Http\Controllers\Catalogos\JurisdiccionController;
use App\Http\Controllers\Catalogos\ModalidadCargaController;
use App\Http\Controllers\Catalogos\NormaController;
use App\Http\Controllers\Catalogos\PeriodicidadController;
use App\Http\Controllers\Catalogos\TipoIndicadorController;
use App\Http\Controllers\Catalogos\TipoJurisdiccionController;
use App\Http\Controllers\Catalogos\UnidadMedidaController;
use App\Http\Controllers\Corridas\CorridaController;
use App\Http\Controllers\DatosFuente\DatoFuenteController;
use App\Http\Controllers\DatosFuente\DatoFuenteApiConfigController;
use App\Http\Controllers\DatosFuente\DatoFuenteApiPathController;
use App\Http\Controllers\DatosFuente\DatoFuenteValorController;
use App\Http\Controllers\DatosFuente\EvidenciaDatoController;
use App\Http\Controllers\Externo\ConsultaExternaController;
use App\Http\Controllers\Indicadores\IndicadorController;
use App\Http\Controllers\Indicadores\IndicadorVariableController;
use App\Http\Controllers\Indicadores\IndicadorVersionController;
use App\Http\Controllers\Observabilidad\AlertaSistemaController;
use App\Http\Controllers\Observabilidad\AuditoriaController;
use App\Http\Controllers\Observabilidad\NotificacionSistemaController;
use App\Http\Controllers\Seguridad\RolController;
use App\Http\Controllers\Seguridad\RolPermisoController;
use App\Http\Controllers\Seguridad\UsuarioController;
use App\Http\Controllers\Seguridad\UsuarioPermisoController;
use App\Http\Controllers\Seguridad\UsuarioRolController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', [AuthController::class, 'perfil']);

    Route::prefix('catalogos')->middleware('permission:indicadores.ver')->group(function () {
        Route::get('categorias', [CategoriaController::class, 'index']);
        Route::get('categorias/{id}', [CategoriaController::class, 'show']);
        Route::get('categorias-tematicas', [CategoriaTematicaController::class, 'index']);
        Route::get('categorias-tematicas/{id}', [CategoriaTematicaController::class, 'show']);
        Route::get('unidades-medida', [UnidadMedidaController::class, 'index']);
        Route::get('unidades-medida/{id}', [UnidadMedidaController::class, 'show']);
        Route::get('periodicidades', [PeriodicidadController::class, 'index']);
        Route::get('periodicidades/{id}', [PeriodicidadController::class, 'show']);
        Route::get('estados-dato', [EstadoDatoController::class, 'index']);
        Route::get('estados-dato/{id}', [EstadoDatoController::class, 'show']);
        Route::get('estados-corrida', [EstadoCorridaController::class, 'index']);
        Route::get('estados-corrida/{id}', [EstadoCorridaController::class, 'show']);
        Route::get('estados-resultado', [EstadoResultadoController::class, 'index']);
        Route::get('estados-resultado/{id}', [EstadoResultadoController::class, 'show']);
        Route::get('modalidades-carga', [ModalidadCargaController::class, 'index']);
        Route::get('modalidades-carga/{id}', [ModalidadCargaController::class, 'show']);
        Route::get('areas-municipales', [AreaMunicipalController::class, 'index']);
        Route::get('areas-municipales/{id}', [AreaMunicipalController::class, 'show']);
        Route::get('fuentes-institucionales', [FuenteInstitucionalController::class, 'index']);
        Route::get('fuentes-institucionales/{id}', [FuenteInstitucionalController::class, 'show']);
        Route::get('tipos-indicador', [TipoIndicadorController::class, 'index']);
        Route::get('tipos-indicador/{id}', [TipoIndicadorController::class, 'show']);
        Route::get('normas', [NormaController::class, 'index']);
        Route::get('normas/{id}', [NormaController::class, 'show']);
        Route::get('tipos-jurisdiccion', [TipoJurisdiccionController::class, 'index']);
        Route::get('tipos-jurisdiccion/{id}', [TipoJurisdiccionController::class, 'show']);
        Route::get('jurisdicciones', [JurisdiccionController::class, 'index']);
        Route::get('jurisdicciones/{id}', [JurisdiccionController::class, 'show']);
    });

    Route::prefix('catalogos')->middleware('permission:indicadores.configurar')->group(function () {
        Route::post('categorias', [CategoriaController::class, 'store']);
        Route::put('categorias/{id}', [CategoriaController::class, 'update']);
        Route::delete('categorias/{id}', [CategoriaController::class, 'destroy']);
        Route::post('categorias-tematicas', [CategoriaTematicaController::class, 'store']);
        Route::put('categorias-tematicas/{id}', [CategoriaTematicaController::class, 'update']);
        Route::delete('categorias-tematicas/{id}', [CategoriaTematicaController::class, 'destroy']);
        Route::post('unidades-medida', [UnidadMedidaController::class, 'store']);
        Route::put('unidades-medida/{id}', [UnidadMedidaController::class, 'update']);
        Route::delete('unidades-medida/{id}', [UnidadMedidaController::class, 'destroy']);
        Route::post('periodicidades', [PeriodicidadController::class, 'store']);
        Route::put('periodicidades/{id}', [PeriodicidadController::class, 'update']);
        Route::delete('periodicidades/{id}', [PeriodicidadController::class, 'destroy']);
        Route::post('estados-dato', [EstadoDatoController::class, 'store']);
        Route::put('estados-dato/{id}', [EstadoDatoController::class, 'update']);
        Route::delete('estados-dato/{id}', [EstadoDatoController::class, 'destroy']);
        Route::post('estados-corrida', [EstadoCorridaController::class, 'store']);
        Route::put('estados-corrida/{id}', [EstadoCorridaController::class, 'update']);
        Route::delete('estados-corrida/{id}', [EstadoCorridaController::class, 'destroy']);
        Route::post('estados-resultado', [EstadoResultadoController::class, 'store']);
        Route::put('estados-resultado/{id}', [EstadoResultadoController::class, 'update']);
        Route::delete('estados-resultado/{id}', [EstadoResultadoController::class, 'destroy']);
        Route::post('modalidades-carga', [ModalidadCargaController::class, 'store']);
        Route::put('modalidades-carga/{id}', [ModalidadCargaController::class, 'update']);
        Route::delete('modalidades-carga/{id}', [ModalidadCargaController::class, 'destroy']);
        Route::post('areas-municipales', [AreaMunicipalController::class, 'store']);
        Route::put('areas-municipales/{id}', [AreaMunicipalController::class, 'update']);
        Route::delete('areas-municipales/{id}', [AreaMunicipalController::class, 'destroy']);
        Route::post('fuentes-institucionales', [FuenteInstitucionalController::class, 'store']);
        Route::put('fuentes-institucionales/{id}', [FuenteInstitucionalController::class, 'update']);
        Route::delete('fuentes-institucionales/{id}', [FuenteInstitucionalController::class, 'destroy']);
        Route::post('tipos-indicador', [TipoIndicadorController::class, 'store']);
        Route::put('tipos-indicador/{id}', [TipoIndicadorController::class, 'update']);
        Route::delete('tipos-indicador/{id}', [TipoIndicadorController::class, 'destroy']);
        Route::post('normas', [NormaController::class, 'store']);
        Route::put('normas/{id}', [NormaController::class, 'update']);
        Route::delete('normas/{id}', [NormaController::class, 'destroy']);
        Route::post('tipos-jurisdiccion', [TipoJurisdiccionController::class, 'store']);
        Route::put('tipos-jurisdiccion/{id}', [TipoJurisdiccionController::class, 'update']);
        Route::delete('tipos-jurisdiccion/{id}', [TipoJurisdiccionController::class, 'destroy']);
        Route::post('jurisdicciones', [JurisdiccionController::class, 'store']);
        Route::put('jurisdicciones/{id}', [JurisdiccionController::class, 'update']);
        Route::delete('jurisdicciones/{id}', [JurisdiccionController::class, 'destroy']);
    });

    Route::prefix('indicadores')->middleware('permission:indicadores.ver')->group(function () {
        Route::get('/', [IndicadorController::class, 'index']);
        Route::get('{id}', [IndicadorController::class, 'show']);
        Route::get('{indicadorId}/versiones', [IndicadorVersionController::class, 'index']);
        Route::get('{indicadorId}/versiones/{id}', [IndicadorVersionController::class, 'show']);
        Route::get('{indicadorId}/versiones/{versionId}/variables', [IndicadorVariableController::class, 'index']);
        Route::get('{indicadorId}/versiones/{versionId}/variables/{id}', [IndicadorVariableController::class, 'show']);
    });

    Route::prefix('indicadores')->middleware('permission:indicadores.configurar')->group(function () {
        Route::post('/', [IndicadorController::class, 'store']);
        Route::put('{id}', [IndicadorController::class, 'update']);
        Route::delete('{id}', [IndicadorController::class, 'destroy']);
        Route::post('{indicadorId}/versiones', [IndicadorVersionController::class, 'store']);
        Route::put('{indicadorId}/versiones/{id}', [IndicadorVersionController::class, 'update']);
        Route::delete('{indicadorId}/versiones/{id}', [IndicadorVersionController::class, 'destroy']);
        Route::post('{indicadorId}/versiones/{versionId}/variables', [IndicadorVariableController::class, 'store']);
        Route::put('{indicadorId}/versiones/{versionId}/variables/{id}', [IndicadorVariableController::class, 'update']);
        Route::delete('{indicadorId}/versiones/{versionId}/variables/{id}', [IndicadorVariableController::class, 'destroy']);
    });

    Route::prefix('datos-fuente')->middleware('permission:datos_fuente.ver')->group(function () {
        Route::get('/', [DatoFuenteController::class, 'index']);
        Route::get('{id}', [DatoFuenteController::class, 'show']);
        Route::get('{datoFuenteId}/valores', [DatoFuenteValorController::class, 'index']);
        Route::get('{datoFuenteId}/valores/{id}', [DatoFuenteValorController::class, 'show']);
        Route::get('{datoFuenteId}/valores/{valorId}/evidencias', [EvidenciaDatoController::class, 'index']);
        Route::get('{datoFuenteId}/valores/{valorId}/evidencias/{id}', [EvidenciaDatoController::class, 'show']);
    });

    Route::prefix('datos-fuente')->middleware('permission:datos_fuente.configurar')->group(function () {
        Route::post('/', [DatoFuenteController::class, 'store']);
        Route::put('{id}', [DatoFuenteController::class, 'update']);
        Route::delete('{id}', [DatoFuenteController::class, 'destroy']);
        Route::get('{datoFuenteId}/api-configs', [DatoFuenteApiConfigController::class, 'index']);
        Route::post('{datoFuenteId}/api-configs', [DatoFuenteApiConfigController::class, 'store']);
        Route::get('{datoFuenteId}/api-configs/{id}', [DatoFuenteApiConfigController::class, 'show']);
        Route::put('{datoFuenteId}/api-configs/{id}', [DatoFuenteApiConfigController::class, 'update']);
        Route::delete('{datoFuenteId}/api-configs/{id}', [DatoFuenteApiConfigController::class, 'destroy']);
        Route::post('{datoFuenteId}/api-configs/{id}/probar', [DatoFuenteApiConfigController::class, 'probar']);
        Route::get('{datoFuenteId}/api-configs/{id}/importaciones', [DatoFuenteApiConfigController::class, 'importaciones']);
        Route::get('{datoFuenteId}/api-configs/{id}/importaciones/{importacionId}', [DatoFuenteApiConfigController::class, 'showImportacion']);
        Route::get('{datoFuenteId}/api-configs/{configId}/paths', [DatoFuenteApiPathController::class, 'index']);
        Route::post('{datoFuenteId}/api-configs/{configId}/paths', [DatoFuenteApiPathController::class, 'store']);
        Route::put('{datoFuenteId}/api-configs/{configId}/paths/{id}', [DatoFuenteApiPathController::class, 'update']);
        Route::delete('{datoFuenteId}/api-configs/{configId}/paths/{id}', [DatoFuenteApiPathController::class, 'destroy']);
    });

    Route::prefix('datos-fuente')->middleware('permission:datos_fuente.cargar')->group(function () {
        Route::post('{datoFuenteId}/valores', [DatoFuenteValorController::class, 'store']);
        Route::put('{datoFuenteId}/valores/{id}', [DatoFuenteValorController::class, 'update']);
        Route::delete('{datoFuenteId}/valores/{id}', [DatoFuenteValorController::class, 'destroy']);
        Route::post('{datoFuenteId}/valores/{valorId}/evidencias', [EvidenciaDatoController::class, 'store']);
        Route::put('{datoFuenteId}/valores/{valorId}/evidencias/{id}', [EvidenciaDatoController::class, 'update']);
        Route::delete('{datoFuenteId}/valores/{valorId}/evidencias/{id}', [EvidenciaDatoController::class, 'destroy']);
        Route::post('{datoFuenteId}/api-configs/{id}/importar', [DatoFuenteApiConfigController::class, 'importar']);
    });

    Route::prefix('datos-fuente')->middleware('permission:datos_fuente.validar')->group(function () {
        Route::post('{datoFuenteId}/valores/{id}/validar', [DatoFuenteValorController::class, 'validar']);
    });

    Route::prefix('seguridad')->middleware('permission:usuarios.gestionar')->group(function () {
        Route::get('usuarios', [UsuarioController::class, 'index']);
        Route::post('usuarios', [UsuarioController::class, 'store']);
        Route::get('usuarios/{id}', [UsuarioController::class, 'show']);
        Route::put('usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy']);
        Route::get('usuarios/{usuarioId}/roles', [UsuarioRolController::class, 'index']);
        Route::post('usuarios/{usuarioId}/roles', [UsuarioRolController::class, 'store']);
        Route::delete('usuarios/{usuarioId}/roles/{rolId}', [UsuarioRolController::class, 'destroy']);
        Route::get('usuarios/{usuarioId}/permisos-efectivos', [UsuarioPermisoController::class, 'efectivos']);
    });

    Route::prefix('seguridad')->middleware('permission:roles.gestionar')->group(function () {
        Route::get('roles', [RolController::class, 'index']);
        Route::post('roles', [RolController::class, 'store']);
        Route::get('roles/{id}', [RolController::class, 'show']);
        Route::put('roles/{id}', [RolController::class, 'update']);
        Route::delete('roles/{id}', [RolController::class, 'destroy']);
        Route::get('roles/{rolId}/permisos', [RolPermisoController::class, 'index']);
        Route::post('roles/{rolId}/permisos', [RolPermisoController::class, 'store']);
        Route::delete('roles/{rolId}/permisos/{permisoId}', [RolPermisoController::class, 'destroy']);
    });

    Route::prefix('corridas')->middleware('permission:indicadores.ver')->group(function () {
        Route::get('/', [CorridaController::class, 'index']);
        Route::get('{id}', [CorridaController::class, 'show']);
    });

    Route::prefix('corridas')->middleware('permission:corridas.ejecutar')->group(function () {
        Route::post('/', [CorridaController::class, 'store']);
        Route::put('{id}', [CorridaController::class, 'update']);
        Route::post('{id}/ejecutar', [CorridaController::class, 'ejecutar']);
    });

    Route::prefix('corridas')->middleware('permission:corridas.aprobar')->group(function () {
        Route::post('{id}/aprobar', [CorridaController::class, 'aprobar']);
    });

    Route::prefix('corridas')->middleware('permission:resultados.publicar')->group(function () {
        Route::post('{id}/publicar', [CorridaController::class, 'publicar']);
    });

    Route::prefix('observabilidad')->middleware('permission:auditoria.ver')->group(function () {
        Route::get('auditoria', [AuditoriaController::class, 'index']);
        Route::get('auditoria/{id}', [AuditoriaController::class, 'show']);
        Route::get('alertas', [AlertaSistemaController::class, 'index']);
        Route::get('alertas/{id}', [AlertaSistemaController::class, 'show']);
        Route::get('notificaciones', [NotificacionSistemaController::class, 'index']);
        Route::get('notificaciones/{id}', [NotificacionSistemaController::class, 'show']);
    });

    Route::prefix('externo')->middleware('permission:indicadores.ver')->group(function () {
        Route::get('indicadores-vigentes', [ConsultaExternaController::class, 'indicadoresVigentes']);
        Route::get('resultados-publicos', [ConsultaExternaController::class, 'resultadosPublicos']);
        Route::get('corridas-publicadas', [ConsultaExternaController::class, 'corridasPublicadas']);
    });
});
