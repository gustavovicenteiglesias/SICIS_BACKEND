<?php

namespace App\Http\Controllers\Observabilidad;

use App\Http\Controllers\Controller;
use App\Models\AlertaSistema;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertaSistemaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AlertaSistema::query()->with(['usuarioAsignado', 'notificaciones']);

        foreach (['estado', 'tipo_alerta', 'severidad', 'usuario_asignado_id', 'entidad_tipo', 'entidad_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('resuelta')) {
            $resolved = filter_var($request->input('resuelta'), FILTER_VALIDATE_BOOLEAN);
            if ($resolved) {
                $query->whereNotNull('resuelta_at');
            } else {
                $query->whereNull('resuelta_at');
            }
        }

        if ($request->filled('fecha_desde')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('fecha_desde'))->startOfDay());
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('fecha_hasta'))->endOfDay());
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json(
            $query->orderByDesc('created_at')->orderByDesc('id')->paginate($perPage)
        );
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(
            AlertaSistema::query()->with(['usuarioAsignado', 'notificaciones.usuario'])->findOrFail($id)
        );
    }
}
