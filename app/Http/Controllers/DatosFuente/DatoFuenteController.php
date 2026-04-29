<?php

namespace App\Http\Controllers\DatosFuente;

use App\Http\Controllers\Controller;
use App\Models\DatoFuente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatoFuenteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DatoFuente::query()->with($this->relations());

        foreach ([
            'area_municipal_id',
            'unidad_medida_id',
            'periodicidad_id',
            'modalidad_carga_id',
            'fuente_institucional_id',
            'responsable_usuario_id',
        ] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->input('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        foreach (['codigo_interno', 'nombre'] as $filter) {
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
        $datoFuente = DatoFuente::create($this->payload($data));

        return response()->json($this->loadDatoFuente($datoFuente), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->loadDatoFuente(DatoFuente::findOrFail($id)));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $datoFuente = DatoFuente::findOrFail($id);
        $data = $request->validate($this->updateRules($datoFuente->id));

        $datoFuente->update($this->payload($data));

        return response()->json($this->loadDatoFuente($datoFuente));
    }

    public function destroy(string $id): JsonResponse
    {
        $datoFuente = DatoFuente::findOrFail($id);
        $datoFuente->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'codigo_interno' => 'required|string|max:120|unique:datos_fuente,codigo_interno',
            'area_municipal_id' => 'nullable|integer|exists:areas_municipales,id',
            'unidad_medida_id' => 'required|integer|exists:unidades_medida,id',
            'periodicidad_id' => 'required|integer|exists:periodicidades,id',
            'modalidad_carga_id' => 'required|integer|exists:modalidades_carga,id',
            'fuente_institucional_id' => 'nullable|integer|exists:fuentes_institucionales,id',
            'responsable_usuario_id' => 'nullable|integer|exists:usuarios,id',
            'nombre' => 'required|string|max:250',
            'descripcion' => 'nullable|string|max:700',
            'tipo_dato' => 'nullable|string|max:50',
            'metodo_obtencion' => 'nullable|string|max:500',
            'link_fuente' => 'nullable|string|max:500',
            'rango_minimo' => 'nullable|numeric',
            'rango_maximo' => 'nullable|numeric|gte:rango_minimo',
            'nivel_geografico' => 'nullable|string|max:120',
            'activo' => 'nullable|boolean',
        ];
    }

    private function updateRules(int $ignoreId): array
    {
        return [
            'codigo_interno' => 'sometimes|required|string|max:120|unique:datos_fuente,codigo_interno,'.$ignoreId,
            'area_municipal_id' => 'nullable|integer|exists:areas_municipales,id',
            'unidad_medida_id' => 'sometimes|required|integer|exists:unidades_medida,id',
            'periodicidad_id' => 'sometimes|required|integer|exists:periodicidades,id',
            'modalidad_carga_id' => 'sometimes|required|integer|exists:modalidades_carga,id',
            'fuente_institucional_id' => 'nullable|integer|exists:fuentes_institucionales,id',
            'responsable_usuario_id' => 'nullable|integer|exists:usuarios,id',
            'nombre' => 'sometimes|required|string|max:250',
            'descripcion' => 'nullable|string|max:700',
            'tipo_dato' => 'nullable|string|max:50',
            'metodo_obtencion' => 'nullable|string|max:500',
            'link_fuente' => 'nullable|string|max:500',
            'rango_minimo' => 'nullable|numeric',
            'rango_maximo' => 'nullable|numeric|gte:rango_minimo',
            'nivel_geografico' => 'nullable|string|max:120',
            'activo' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'codigo_interno',
            'area_municipal_id',
            'unidad_medida_id',
            'periodicidad_id',
            'modalidad_carga_id',
            'fuente_institucional_id',
            'responsable_usuario_id',
            'nombre',
            'descripcion',
            'tipo_dato',
            'metodo_obtencion',
            'link_fuente',
            'rango_minimo',
            'rango_maximo',
            'nivel_geografico',
            'activo',
        ])->all();
    }

    private function loadDatoFuente(DatoFuente $datoFuente): DatoFuente
    {
        return $datoFuente->fresh($this->relations());
    }

    private function relations(): array
    {
        return [
            'areaMunicipal',
            'unidadMedida',
            'periodicidad',
            'modalidadCarga',
            'fuenteInstitucional',
            'responsableUsuario',
        ];
    }
}
