<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\UsuarioRol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioRolController extends Controller
{
    public function index(string $usuarioId): JsonResponse
    {
        $usuario = Usuario::with('roles')->findOrFail($usuarioId);

        return response()->json([
            'data' => $usuario->roles->values(),
        ]);
    }

    public function store(Request $request, string $usuarioId): JsonResponse
    {
        $usuario = Usuario::findOrFail($usuarioId);
        $data = $request->validate([
            'rol_id' => 'required|integer|exists:roles,id',
        ]);

        Rol::findOrFail($data['rol_id']);

        UsuarioRol::withTrashed()->updateOrCreate(
            [
                'usuario_id' => $usuario->id,
                'rol_id' => $data['rol_id'],
            ],
            [
                'deleted_at' => null,
            ]
        );

        return response()->json([
            'usuario' => $usuario->fresh('roles'),
        ], 201);
    }

    public function destroy(string $usuarioId, string $rolId): JsonResponse
    {
        $usuario = Usuario::findOrFail($usuarioId);

        $asignacion = UsuarioRol::query()
            ->where('usuario_id', $usuario->id)
            ->where('rol_id', $rolId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $asignacion->delete();

        return response()->noContent();
    }
}
