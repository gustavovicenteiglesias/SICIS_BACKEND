<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Usuario::query()->with(['areaMunicipal', 'roles']);

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->input('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('area_municipal_id')) {
            $query->where('area_municipal_id', $request->input('area_municipal_id'));
        }

        foreach (['nombre_usuario', 'email', 'nombre', 'apellido'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'like', '%'.$request->input($filter).'%');
            }
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json(
            $query->orderBy('apellido')->orderBy('nombre')->paginate($perPage)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->storeRules());
        $usuario = Usuario::create($this->payload($data));

        return response()->json($this->loadUsuario($usuario), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->loadUsuario(Usuario::findOrFail($id)));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $data = $request->validate($this->updateRules($usuario->id));

        $usuario->update($this->payload($data));

        return response()->json($this->loadUsuario($usuario));
    }

    public function destroy(string $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'area_municipal_id' => 'nullable|integer|exists:areas_municipales,id',
            'nombre_usuario' => 'required|string|max:150|unique:usuarios,nombre_usuario',
            'nombre' => 'required|string|max:150',
            'apellido' => 'required|string|max:150',
            'email' => 'required|email|max:180|unique:usuarios,email',
            'password' => 'required|string|min:8',
            'activo' => 'nullable|boolean',
        ];
    }

    private function updateRules(int $ignoreId): array
    {
        return [
            'area_municipal_id' => 'nullable|integer|exists:areas_municipales,id',
            'nombre_usuario' => 'sometimes|required|string|max:150|unique:usuarios,nombre_usuario,'.$ignoreId,
            'nombre' => 'sometimes|required|string|max:150',
            'apellido' => 'sometimes|required|string|max:150',
            'email' => 'sometimes|required|email|max:180|unique:usuarios,email,'.$ignoreId,
            'password' => 'nullable|string|min:8',
            'activo' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'area_municipal_id',
            'nombre_usuario',
            'nombre',
            'apellido',
            'email',
            'password',
            'activo',
        ])->filter(function ($value, $key) {
            return $key !== 'password' || filled($value);
        })->all();
    }

    private function loadUsuario(Usuario $usuario): Usuario
    {
        return $usuario->fresh(['areaMunicipal', 'roles']);
    }
}
