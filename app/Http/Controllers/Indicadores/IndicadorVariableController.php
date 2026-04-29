<?php

namespace App\Http\Controllers\Indicadores;

use App\Http\Controllers\Controller;
use App\Models\IndicadorVariable;
use App\Models\IndicadorVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndicadorVariableController extends Controller
{
    public function index(Request $request, string $indicadorId, string $versionId): JsonResponse
    {
        $version = $this->findVersion($indicadorId, $versionId);

        $query = $version->variables()->with('datoFuente');

        foreach (['dato_fuente_id', 'codigo_variable', 'rol'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('obligatorio')) {
            $query->where('obligatorio', filter_var($request->input('obligatorio'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->orderBy('orden')->paginate($perPage));
    }

    public function store(Request $request, string $indicadorId, string $versionId): JsonResponse
    {
        $version = $this->findVersion($indicadorId, $versionId);
        $data = $request->validate($this->storeRules($version->id));

        $variable = $version->variables()->create($this->variableData($data));

        return response()->json($this->loadVariable($variable), 201);
    }

    public function show(string $indicadorId, string $versionId, string $id): JsonResponse
    {
        return response()->json($this->findVariable($indicadorId, $versionId, $id));
    }

    public function update(Request $request, string $indicadorId, string $versionId, string $id): JsonResponse
    {
        $variable = $this->findVariable($indicadorId, $versionId, $id);
        $data = $request->validate($this->updateRules($variable->indicador_version_id, $variable->id));

        $variable->update($this->variableData($data));

        return response()->json($this->loadVariable($variable));
    }

    public function destroy(string $indicadorId, string $versionId, string $id): JsonResponse
    {
        $variable = $this->findVariable($indicadorId, $versionId, $id);
        $variable->delete();

        return response()->noContent();
    }

    private function storeRules(int $versionId): array
    {
        return [
            'dato_fuente_id' => 'required|integer|exists:datos_fuente,id',
            'codigo_variable' => ['required', 'string', 'max:100', function (string $attribute, mixed $value, \Closure $fail) use ($versionId): void {
                $exists = IndicadorVariable::query()
                    ->where('indicador_version_id', $versionId)
                    ->where('codigo_variable', $value)
                    ->exists();

                if ($exists) {
                    $fail('El codigo de variable ya existe para esta version.');
                }
            }],
            'rol' => 'required|string|max:80',
            'obligatorio' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:1',
            'descripcion' => 'nullable|string|max:300',
        ];
    }

    private function updateRules(int $versionId, int $ignoreId): array
    {
        return [
            'dato_fuente_id' => 'sometimes|required|integer|exists:datos_fuente,id',
            'codigo_variable' => ['sometimes', 'required', 'string', 'max:100', function (string $attribute, mixed $value, \Closure $fail) use ($versionId, $ignoreId): void {
                $exists = IndicadorVariable::query()
                    ->where('indicador_version_id', $versionId)
                    ->where('codigo_variable', $value)
                    ->where('id', '!=', $ignoreId)
                    ->exists();

                if ($exists) {
                    $fail('El codigo de variable ya existe para esta version.');
                }
            }],
            'rol' => 'sometimes|required|string|max:80',
            'obligatorio' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:1',
            'descripcion' => 'nullable|string|max:300',
        ];
    }

    private function variableData(array $data): array
    {
        return collect($data)->only([
            'dato_fuente_id',
            'codigo_variable',
            'rol',
            'obligatorio',
            'orden',
            'descripcion',
        ])->all();
    }

    private function findVersion(string $indicadorId, string $versionId): IndicadorVersion
    {
        return IndicadorVersion::query()
            ->where('indicador_id', $indicadorId)
            ->findOrFail($versionId);
    }

    private function findVariable(string $indicadorId, string $versionId, string $id): IndicadorVariable
    {
        $version = $this->findVersion($indicadorId, $versionId);

        return $version->variables()->with('datoFuente')->findOrFail($id);
    }

    private function loadVariable(IndicadorVariable $variable): IndicadorVariable
    {
        return $variable->fresh('datoFuente');
    }
}
