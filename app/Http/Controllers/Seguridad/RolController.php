<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Rol::query()->with('permisos');

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->input('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        foreach (['codigo', 'nombre'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'like', '%'.$request->input($filter).'%');
            }
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->orderBy('nombre')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->storeRules());
        $rol = Rol::create($this->payload($data));

        return response()->json($this->loadRol($rol), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->loadRol(Rol::findOrFail($id)));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $rol = Rol::findOrFail($id);
        $data = $request->validate($this->updateRules($rol->id));

        $rol->update($this->payload($data));

        return response()->json($this->loadRol($rol));
    }

    public function destroy(string $id): JsonResponse
    {
        $rol = Rol::findOrFail($id);
        $rol->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'codigo' => 'required|string|max:80|unique:roles,codigo',
            'nombre' => 'required|string|max:120|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
            'activo' => 'nullable|boolean',
        ];
    }

    private function updateRules(int $ignoreId): array
    {
        return [
            'codigo' => 'sometimes|required|string|max:80|unique:roles,codigo,'.$ignoreId,
            'nombre' => 'sometimes|required|string|max:120|unique:roles,nombre,'.$ignoreId,
            'descripcion' => 'nullable|string|max:255',
            'activo' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'codigo',
            'nombre',
            'descripcion',
            'activo',
        ])->all();
    }

    private function loadRol(Rol $rol): Rol
    {
        return $rol->fresh('permisos');
    }
}
