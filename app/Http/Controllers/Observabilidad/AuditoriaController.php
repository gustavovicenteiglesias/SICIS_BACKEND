<?php

namespace App\Http\Controllers\Observabilidad;

use App\Http\Controllers\Controller;
use App\Models\AuditoriaLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AuditoriaLog::query()->with('usuario');

        foreach (['usuario_id', 'tabla_afectada', 'registro_id', 'accion'] as $filter) {
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
            AuditoriaLog::query()->with('usuario')->findOrFail($id)
        );
    }
}
