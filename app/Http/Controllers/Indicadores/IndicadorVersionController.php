<?php

namespace App\Http\Controllers\Indicadores;

use App\Http\Controllers\Controller;
use App\Models\Indicador;
use App\Models\IndicadorVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IndicadorVersionController extends Controller
{
    public function index(Request $request, string $indicadorId): JsonResponse
    {
        $indicador = Indicador::findOrFail($indicadorId);

        $query = $indicador->versiones()
            ->with(['tipoIndicador', 'unidadMedida', 'periodicidad', 'variables']);

        if ($request->filled('activa')) {
            $query->where('activa', filter_var($request->input('activa'), FILTER_VALIDATE_BOOLEAN));
        }

        foreach (['version', 'tipo_indicador_id', 'unidad_medida_id', 'periodicidad_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('vigente_desde')) {
            $query->whereDate('vigente_desde', '>=', $request->input('vigente_desde'));
        }

        if ($request->filled('vigente_hasta')) {
            $query->whereDate('vigente_hasta', '<=', $request->input('vigente_hasta'));
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->orderByDesc('vigente_desde')->paginate($perPage));
    }

    public function store(Request $request, string $indicadorId): JsonResponse
    {
        $indicador = Indicador::findOrFail($indicadorId);
        $data = $request->validate($this->storeRules($indicador->id));
        $this->ensureNoOverlap($indicador->id, $data);

        $version = $indicador->versiones()->create($this->versionData($data));

        return response()->json($this->loadVersion($version), 201);
    }

    public function show(string $indicadorId, string $id): JsonResponse
    {
        return response()->json($this->findVersion($indicadorId, $id));
    }

    public function update(Request $request, string $indicadorId, string $id): JsonResponse
    {
        $version = $this->findVersion($indicadorId, $id);
        $data = $request->validate($this->updateRules((int) $indicadorId, $version->id));
        $candidate = array_merge($version->toArray(), $data);
        $this->ensureDateOrder($candidate);
        $this->ensureNoOverlap((int) $indicadorId, $candidate, $version->id);

        $version->update($this->versionData($data));

        return response()->json($this->loadVersion($version));
    }

    public function destroy(string $indicadorId, string $id): JsonResponse
    {
        $version = $this->findVersion($indicadorId, $id);
        $version->delete();

        return response()->noContent();
    }

    private function storeRules(int $indicadorId): array
    {
        return [
            'tipo_indicador_id' => 'required|integer|exists:tipos_indicador,id',
            'unidad_medida_id' => 'required|integer|exists:unidades_medida,id',
            'periodicidad_id' => 'required|integer|exists:periodicidades,id',
            'version' => ['required', 'string', 'max:40', function (string $attribute, mixed $value, \Closure $fail) use ($indicadorId): void {
                $exists = IndicadorVersion::query()
                    ->where('indicador_id', $indicadorId)
                    ->where('version', $value)
                    ->exists();

                if ($exists) {
                    $fail('La version ya existe para este indicador.');
                }
            }],
            'formula_tipo' => 'nullable|string|max:80',
            'constante' => 'nullable|numeric',
            'formula_texto' => 'required|string|max:700',
            'formula_expression' => 'nullable|string|max:1000',
            'objetivo' => 'nullable|string|max:700',
            'observaciones_metodologicas' => 'nullable|string|max:1000',
            'vigente_desde' => 'required|date',
            'vigente_hasta' => 'nullable|date|after_or_equal:vigente_desde',
            'activa' => 'nullable|boolean',
        ];
    }

    private function updateRules(int $indicadorId, int $ignoreId): array
    {
        return [
            'tipo_indicador_id' => 'sometimes|required|integer|exists:tipos_indicador,id',
            'unidad_medida_id' => 'sometimes|required|integer|exists:unidades_medida,id',
            'periodicidad_id' => 'sometimes|required|integer|exists:periodicidades,id',
            'version' => ['sometimes', 'required', 'string', 'max:40', function (string $attribute, mixed $value, \Closure $fail) use ($indicadorId, $ignoreId): void {
                $exists = IndicadorVersion::query()
                    ->where('indicador_id', $indicadorId)
                    ->where('version', $value)
                    ->where('id', '!=', $ignoreId)
                    ->exists();

                if ($exists) {
                    $fail('La version ya existe para este indicador.');
                }
            }],
            'formula_tipo' => 'nullable|string|max:80',
            'constante' => 'nullable|numeric',
            'formula_texto' => 'sometimes|required|string|max:700',
            'formula_expression' => 'nullable|string|max:1000',
            'objetivo' => 'nullable|string|max:700',
            'observaciones_metodologicas' => 'nullable|string|max:1000',
            'vigente_desde' => 'sometimes|required|date',
            'vigente_hasta' => 'nullable|date',
            'activa' => 'nullable|boolean',
        ];
    }

    private function versionData(array $data): array
    {
        return collect($data)->only([
            'tipo_indicador_id',
            'unidad_medida_id',
            'periodicidad_id',
            'version',
            'formula_tipo',
            'constante',
            'formula_texto',
            'formula_expression',
            'objetivo',
            'observaciones_metodologicas',
            'vigente_desde',
            'vigente_hasta',
            'activa',
        ])->all();
    }

    private function findVersion(string $indicadorId, string $id): IndicadorVersion
    {
        return IndicadorVersion::query()
            ->where('indicador_id', $indicadorId)
            ->with(['tipoIndicador', 'unidadMedida', 'periodicidad', 'variables.datoFuente'])
            ->findOrFail($id);
    }

    private function loadVersion(IndicadorVersion $version): IndicadorVersion
    {
        return $version->fresh(['tipoIndicador', 'unidadMedida', 'periodicidad', 'variables.datoFuente']);
    }

    private function ensureNoOverlap(int $indicadorId, array $data, ?int $ignoreId = null): void
    {
        $activa = array_key_exists('activa', $data) ? (bool) $data['activa'] : true;

        if (!$activa) {
            return;
        }

        $desde = $data['vigente_desde'] ?? null;
        $hasta = $data['vigente_hasta'] ?? null;

        if (!$desde) {
            return;
        }

        $query = IndicadorVersion::query()
            ->where('indicador_id', $indicadorId)
            ->where('activa', true)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($q) use ($desde, $hasta): void {
                $q->where(function ($sub) use ($desde, $hasta): void {
                    $sub->where('vigente_desde', '<=', $hasta ?? '9999-12-31')
                        ->where(function ($limit) use ($desde): void {
                            $limit->whereNull('vigente_hasta')
                                ->orWhere('vigente_hasta', '>=', $desde);
                        });
                });
            });

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'vigente_desde' => ['Existe otra version activa superpuesta para este indicador.'],
            ]);
        }
    }

    private function ensureDateOrder(array $data): void
    {
        $desde = $data['vigente_desde'] ?? null;
        $hasta = $data['vigente_hasta'] ?? null;

        if ($desde && $hasta && $hasta < $desde) {
            throw ValidationException::withMessages([
                'vigente_hasta' => ['La fecha de fin debe ser igual o posterior a la fecha de inicio.'],
            ]);
        }
    }
}
