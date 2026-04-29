<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_municipal_id')->nullable()->constrained('areas_municipales')->nullOnDelete()->cascadeOnUpdate();
            $table->string('nombre_usuario', 150)->unique();
            $table->string('nombre', 150);
            $table->string('apellido', 150);
            $table->string('email', 180)->unique();
            $table->string('password');
            $table->boolean('activo')->default(true);
            $table->dateTime('ultimo_acceso_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
