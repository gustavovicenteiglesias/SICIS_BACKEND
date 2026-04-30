<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\RolPermiso;
use App\Support\Observability\Observability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolPermisoController extends Controller
{
    public function index(string $rolId): JsonResponse
    {
        $rol = Rol::with('permisos')->findOrFail($rolId);

        return response()->json([
            'data' => $rol->permisos->values(),
        ]);
    }

    public function store(Request $request, string $rolId): JsonResponse
    {
        $rol = Rol::findOrFail($rolId);
        $data = $request->validate([
            'permiso_id' => 'required|integer|exists:permisos,id',
        ]);

        Permiso::findOrFail($data['permiso_id']);

        RolPermiso::firstOrCreate([
            'rol_id' => $rol->id,
            'permiso_id' => $data['permiso_id'],
        ]);

        Observability::audit($request, 'roles_permisos', $rol->id, 'ASIGNAR_PERMISO', null, [
            'rol_id' => $rol->id,
            'permiso_id' => $data['permiso_id'],
        ]);

        return response()->json([
            'rol' => $rol->fresh('permisos'),
        ], 201);
    }

    public function destroy(string $rolId, string $permisoId): JsonResponse
    {
        $rol = Rol::findOrFail($rolId);

        $asignacion = RolPermiso::query()
            ->where('rol_id', $rol->id)
            ->where('permiso_id', $permisoId)
            ->firstOrFail();

        $before = $asignacion->withoutRelations()->toArray();
        $asignacion->delete();
        Observability::audit(request(), 'roles_permisos', $rol->id, 'QUITAR_PERMISO', $before, null);

        return response()->noContent();
    }
}
