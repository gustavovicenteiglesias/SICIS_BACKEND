<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_fuente_api_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dato_fuente_id')->constrained('datos_fuente')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 180);
            $table->string('metodo_http', 10)->default('GET');
            $table->string('url', 700);
            $table->string('auth_tipo', 50)->nullable();
            $table->json('headers_json')->nullable();
            $table->json('params_json')->nullable();
            $table->string('json_path_valor');
            $table->string('json_path_periodo')->nullable();
            $table->string('json_path_jurisdiccion')->nullable();
            $table->string('unidad_esperada', 80)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('datos_fuente_api_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dato_fuente_api_config_id')->constrained('datos_fuente_api_configs')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('json_path_valor');
            $table->unsignedSmallInteger('prioridad')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('datos_fuente_api_importaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dato_fuente_api_config_id')->constrained('datos_fuente_api_configs')->restrictOnDelete()->cascadeOnUpdate();
            $table->dateTime('fecha_importacion')->useCurrent();
            $table->string('estado', 50);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('json_path_usado')->nullable();
            $table->decimal('valor_extraido', 20, 6)->nullable();
            $table->string('mensaje_error', 700)->nullable();
            $table->json('muestra_respuesta')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('fecha_importacion');
        });

        Schema::create('corridas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurisdiccion_id')->constrained('jurisdicciones')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('estado_corrida_id')->constrained('estados_corrida')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('titulo', 250);
            $table->date('periodo_referencia');
            $table->foreignId('usuario_ejecucion_id')->constrained('usuarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('usuario_aprobacion_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->dateTime('ejecutada_at')->nullable();
            $table->dateTime('aprobada_at')->nullable();
            $table->dateTime('publicada_at')->nullable();
            $table->string('observaciones', 700)->nullable();
            $table->timestamps();

            $table->index('periodo_referencia');
        });

        Schema::create('corridas_snapshot_datos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corrida_id')->constrained('corridas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('dato_fuente_id')->constrained('datos_fuente')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('dato_fuente_valor_id')->constrained('datos_fuente_valores')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('valor_dato', 20, 6);
            $table->date('periodo_referencia');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['corrida_id', 'dato_fuente_id'], 'uk_snapshot_datos_corrida_dato');
        });

        Schema::create('corridas_snapshot_indicadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corrida_id')->constrained('corridas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('indicador_id')->constrained('indicadores')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('indicador_version_id')->constrained('indicadores_versiones')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('estado_resultado_id')->constrained('estados_resultado')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('valor_resultado', 20, 6)->nullable();
            $table->string('formula_tipo', 80);
            $table->decimal('constante', 20, 6)->default(1);
            $table->string('formula_texto_usada', 700);
            $table->string('formula_expression_usada', 1000)->nullable();
            $table->boolean('publicable_en_corrida')->default(true);
            $table->date('periodo_referencia');
            $table->string('observaciones', 700)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['corrida_id', 'indicador_id'], 'uk_snapshot_ind_corrida_indicador');
        });

        Schema::create('alertas_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_alerta', 100);
            $table->string('severidad', 40);
            $table->string('titulo', 250);
            $table->string('mensaje', 1000);
            $table->string('entidad_tipo', 100)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('estado', 50)->default('PENDIENTE');
            $table->foreignId('usuario_asignado_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->dateTime('resuelta_at')->nullable();
            $table->timestamps();

            $table->index('estado');
            $table->index('tipo_alerta');
            $table->index(['entidad_tipo', 'entidad_id'], 'idx_alertas_entidad');
        });

        Schema::create('notificaciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alerta_id')->nullable()->constrained('alertas_sistema')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->string('canal', 50);
            $table->string('destinatario');
            $table->string('asunto');
            $table->string('cuerpo', 2000);
            $table->string('estado', 50)->default('PENDIENTE');
            $table->unsignedSmallInteger('intentos')->default(0);
            $table->string('ultimo_error', 700)->nullable();
            $table->dateTime('enviada_at')->nullable();
            $table->timestamps();

            $table->index('estado');
        });

        Schema::create('auditoria_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->string('tabla_afectada', 120);
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->string('accion', 60);
            $table->json('valor_anterior')->nullable();
            $table->json('valor_nuevo')->nullable();
            $table->string('motivo', 500)->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index(['tabla_afectada', 'registro_id'], 'idx_auditoria_tabla_registro');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_logs');
        Schema::dropIfExists('notificaciones_sistema');
        Schema::dropIfExists('alertas_sistema');
        Schema::dropIfExists('corridas_snapshot_indicadores');
        Schema::dropIfExists('corridas_snapshot_datos');
        Schema::dropIfExists('corridas');
        Schema::dropIfExists('datos_fuente_api_importaciones');
        Schema::dropIfExists('datos_fuente_api_paths');
        Schema::dropIfExists('datos_fuente_api_configs');
    }
};
