<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;

class UsuarioPermisoController extends Controller
{
    public function efectivos(string $usuarioId): JsonResponse
    {
        $usuario = Usuario::with('roles.permisos')->findOrFail($usuarioId);

        return response()->json([
            'usuario_id' => $usuario->id,
            'permisos' => $usuario->permisos()->map(fn ($permiso) => [
                'id' => $permiso->id,
                'codigo' => $permiso->codigo,
                'nombre' => $permiso->nombre,
            ])->values(),
        ]);
    }
}
