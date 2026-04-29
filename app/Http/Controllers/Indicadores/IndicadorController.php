<?php

namespace App\Http\Controllers\Indicadores;

use App\Http\Controllers\Controller;
use App\Models\Indicador;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndicadorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Indicador::query()
            ->with(['categoria', 'categoriaTematica', 'normas']);

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->input('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('publicable')) {
            $query->where('publicable', filter_var($request->input('publicable'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('sensible')) {
            $query->where('sensible', filter_var($request->input('sensible'), FILTER_VALIDATE_BOOLEAN));
        }

        foreach (['categoria_id', 'categoria_tematica_id', 'codigo_interno', 'nombre'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->orderBy('orden')->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->storeRules());

        $indicador = Indicador::create($this->indicatorData($data));
        $this->syncNormas($indicador, $data['normas'] ?? []);

        return response()->json($this->loadIndicador($indicador), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->loadIndicador(Indicador::findOrFail($id)));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $indicador = Indicador::findOrFail($id);
        $data = $request->validate($this->updateRules($indicador->id));

        $indicador->update($this->indicatorData($data));

        if (array_key_exists('normas', $data)) {
            $this->syncNormas($indicador, $data['normas'] ?? []);
        }

        return response()->json($this->loadIndicador($indicador));
    }

    public function destroy(string $id): JsonResponse
    {
        $indicador = Indicador::findOrFail($id);
        $indicador->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'codigo_interno' => 'required|string|max:100|unique:indicadores,codigo_interno',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'categoria_tematica_id' => 'nullable|integer|exists:categorias_tematicas,id',
            'nombre' => 'required|string|max:250',
            'descripcion' => 'required|string|max:700',
            'publicable' => 'nullable|boolean',
            'sensible' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:1',
            'normas' => 'nullable|array',
            'normas.*.norma_id' => 'required|integer|exists:normas,id',
            'normas.*.codigo_en_norma' => 'nullable|string|max:100',
            'normas.*.nombre_en_norma' => 'nullable|string|max:255',
        ];
    }

    private function updateRules(int $ignoreId): array
    {
        return [
            'codigo_interno' => 'sometimes|required|string|max:100|unique:indicadores,codigo_interno,'.$ignoreId,
            'categoria_id' => 'sometimes|required|integer|exists:categorias,id',
            'categoria_tematica_id' => 'nullable|integer|exists:categorias_tematicas,id',
            'nombre' => 'sometimes|required|string|max:250',
            'descripcion' => 'sometimes|required|string|max:700',
            'publicable' => 'nullable|boolean',
            'sensible' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:1',
            'normas' => 'nullable|array',
            'normas.*.norma_id' => 'required|integer|exists:normas,id',
            'normas.*.codigo_en_norma' => 'nullable|string|max:100',
            'normas.*.nombre_en_norma' => 'nullable|string|max:255',
        ];
    }

    private function indicatorData(array $data): array
    {
        return collect($data)->only([
            'codigo_interno',
            'categoria_id',
            'categoria_tematica_id',
            'nombre',
            'descripcion',
            'publicable',
            'sensible',
            'activo',
            'orden',
        ])->all();
    }

    private function syncNormas(Indicador $indicador, array $normas): void
    {
        $syncData = [];

        foreach ($normas as $norma) {
            $syncData[$norma['norma_id']] = [
                'codigo_en_norma' => $norma['codigo_en_norma'] ?? null,
                'nombre_en_norma' => $norma['nombre_en_norma'] ?? null,
            ];
        }

        $indicador->normas()->sync($syncData);
    }

    private function loadIndicador(Indicador $indicador): Indicador
    {
        return $indicador->fresh(['categoria', 'categoriaTematica', 'normas']);
    }
}
