<?php

namespace App\Http\Controllers\DatosFuente;

use App\Http\Controllers\Controller;
use App\Models\DatoFuenteApiConfig;
use App\Models\DatoFuenteApiPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatoFuenteApiPathController extends Controller
{
    public function index(Request $request, string $datoFuenteId, string $configId): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $configId);
        $query = $config->paths();

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->input('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request, string $datoFuenteId, string $configId): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $configId);
        $data = $request->validate($this->storeRules());

        $path = $config->paths()->create($this->payload($data));

        return response()->json($path->fresh(), 201);
    }

    public function update(Request $request, string $datoFuenteId, string $configId, string $id): JsonResponse
    {
        $path = $this->findPath($datoFuenteId, $configId, $id);
        $data = $request->validate($this->updateRules());

        $path->update($this->payload($data));

        return response()->json($path->fresh());
    }

    public function destroy(string $datoFuenteId, string $configId, string $id): JsonResponse
    {
        $path = $this->findPath($datoFuenteId, $configId, $id);
        $path->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'json_path_valor' => 'required|string|max:255',
            'prioridad' => 'nullable|integer|min:1',
            'activo' => 'nullable|boolean',
        ];
    }

    private function updateRules(): array
    {
        return [
            'json_path_valor' => 'sometimes|required|string|max:255',
            'prioridad' => 'nullable|integer|min:1',
            'activo' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'json_path_valor',
            'prioridad',
            'activo',
        ])->all();
    }

    private function findConfig(string $datoFuenteId, string $configId): DatoFuenteApiConfig
    {
        return DatoFuenteApiConfig::query()
            ->where('dato_fuente_id', $datoFuenteId)
            ->findOrFail($configId);
    }

    private function findPath(string $datoFuenteId, string $configId, string $id): DatoFuenteApiPath
    {
        $config = $this->findConfig($datoFuenteId, $configId);

        return $config->paths()->findOrFail($id);
    }
}
