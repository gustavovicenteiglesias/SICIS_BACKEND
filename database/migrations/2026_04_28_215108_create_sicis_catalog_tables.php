<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 120)->unique();
            $table->string('nombre', 150);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('permiso_id')->constrained('permisos')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['rol_id', 'permiso_id'], 'uk_roles_permisos_rol_permiso');
        });

        Schema::create('usuarios_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('rol_id')->constrained('roles')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['usuario_id', 'rol_id'], 'uk_usuarios_roles_usuario_rol');
        });

        Schema::create('normas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40);
            $table->string('nombre', 250);
            $table->string('version', 40)->nullable();
            $table->unsignedSmallInteger('anio')->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['codigo', 'version'], 'uk_normas_codigo_version');
        });

        Schema::create('tipos_indicador', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 120);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 180)->unique();
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(1);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categorias_tematicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 180);
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(1);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['categoria_id', 'nombre'], 'uk_cat_tematicas_categoria_nombre');
        });

        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->string('simbolo', 50)->nullable();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('periodicidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 80)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('estados_dato', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 80);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('estados_corrida', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('estados_resultado', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('modalidades_carga', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 80);
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('fuentes_institucionales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 220)->unique();
            $table->string('organismo', 220)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->string('url_base', 500)->nullable();
            $table->string('responsable')->nullable();
            $table->string('contacto')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuentes_institucionales');
        Schema::dropIfExists('modalidades_carga');
        Schema::dropIfExists('estados_resultado');
        Schema::dropIfExists('estados_corrida');
        Schema::dropIfExists('estados_dato');
        Schema::dropIfExists('periodicidades');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('categorias_tematicas');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('tipos_indicador');
        Schema::dropIfExists('normas');
        Schema::dropIfExists('usuarios_roles');
        Schema::dropIfExists('roles_permisos');
        Schema::dropIfExists('permisos');
    }
};
