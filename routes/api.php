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
use App\Http\Controllers\Indicadores\IndicadorController;
use App\Http\Controllers\Indicadores\IndicadorVariableController;
use App\Http\Controllers\Indicadores\IndicadorVersionController;
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
});
