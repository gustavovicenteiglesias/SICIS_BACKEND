<?php

namespace App\Http\Controllers\Observabilidad;

use App\Http\Controllers\Controller;
use App\Models\NotificacionSistema;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificacionSistemaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = NotificacionSistema::query()->with(['alerta', 'usuario']);

        foreach (['estado', 'canal', 'usuario_id', 'alerta_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
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
            NotificacionSistema::query()->with(['alerta', 'usuario'])->findOrFail($id)
        );
    }
}
