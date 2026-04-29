<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_jurisdiccion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('jurisdicciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_jurisdiccion_id')->constrained('tipos_jurisdiccion')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('jurisdiccion_padre_id')->nullable()->constrained('jurisdicciones')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 220);
            $table->string('codigo_oficial', 80)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
        });

        Schema::create('indicadores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno', 100)->unique();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('categoria_tematica_id')->nullable()->constrained('categorias_tematicas')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 250);
            $table->string('descripcion', 700);
            $table->boolean('publicable')->default(true);
            $table->boolean('sensible')->default(false);
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('indicadores_normas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicadores')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('codigo_en_norma', 100)->nullable();
            $table->string('nombre_en_norma')->nullable();
            $table->timestamps();

            $table->unique(['indicador_id', 'norma_id'], 'uk_indicadores_normas_indicador_norma');
        });

        Schema::create('indicadores_versiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_id')->constrained('indicadores')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('tipo_indicador_id')->constrained('tipos_indicador')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('periodicidad_id')->constrained('periodicidades')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('version', 40);
            $table->string('formula_tipo', 80)->default('RATIO_CONSTANTE');
            $table->decimal('constante', 20, 6)->default(1);
            $table->string('formula_texto', 700);
            $table->string('formula_expression', 1000)->nullable();
            $table->string('objetivo', 700)->nullable();
            $table->string('observaciones_metodologicas', 1000)->nullable();
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['indicador_id', 'version'], 'uk_indicadores_versiones_ind_version');
        });

        Schema::create('datos_fuente', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_interno', 120)->unique();
            $table->foreignId('area_municipal_id')->nullable()->constrained('areas_municipales')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('periodicidad_id')->constrained('periodicidades')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('modalidad_carga_id')->constrained('modalidades_carga')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('fuente_institucional_id')->nullable()->constrained('fuentes_institucionales')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('responsable_usuario_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 250);
            $table->string('descripcion', 700)->nullable();
            $table->string('tipo_dato', 50)->default('decimal');
            $table->string('metodo_obtencion', 500)->nullable();
            $table->string('link_fuente', 500)->nullable();
            $table->decimal('rango_minimo', 20, 6)->nullable();
            $table->decimal('rango_maximo', 20, 6)->nullable();
            $table->string('nivel_geografico', 120)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('indicadores_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_version_id')->constrained('indicadores_versiones')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('dato_fuente_id')->constrained('datos_fuente')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('codigo_variable', 100);
            $table->string('rol', 80);
            $table->boolean('obligatorio')->default(true);
            $table->unsignedSmallInteger('orden')->default(1);
            $table->string('descripcion', 300)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['indicador_version_id', 'codigo_variable'], 'uk_ind_variables_version_codigo');
        });

        Schema::create('datos_fuente_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dato_fuente_id')->constrained('datos_fuente')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('jurisdiccion_id')->constrained('jurisdicciones')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('estado_dato_id')->constrained('estados_dato')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('modalidad_carga_id')->constrained('modalidades_carga')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('usuario_carga_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('usuario_valida_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->decimal('valor_crudo', 20, 6);
            $table->decimal('valor_utilizado', 20, 6)->nullable();
            $table->date('periodo_referencia');
            $table->date('fecha_produccion')->nullable();
            $table->dateTime('fecha_carga')->useCurrent();
            $table->dateTime('validado_at')->nullable();
            $table->string('observado_motivo', 700)->nullable();
            $table->boolean('vigente')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['dato_fuente_id', 'jurisdiccion_id', 'periodo_referencia'], 'idx_df_valores_dato_jur_periodo');
            $table->index('periodo_referencia');
        });

        Schema::create('evidencias_dato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dato_fuente_valor_id')->constrained('datos_fuente_valores')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre_archivo')->nullable();
            $table->string('url', 500)->nullable();
            $table->string('hash_archivo', 128)->nullable();
            $table->string('descripcion', 700)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias_dato');
        Schema::dropIfExists('datos_fuente_valores');
        Schema::dropIfExists('indicadores_variables');
        Schema::dropIfExists('datos_fuente');
        Schema::dropIfExists('indicadores_versiones');
        Schema::dropIfExists('indicadores_normas');
        Schema::dropIfExists('indicadores');
        Schema::dropIfExists('jurisdicciones');
        Schema::dropIfExists('tipos_jurisdiccion');
    }
};
