<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Jurisdiccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurisdiccionController extends BaseCatalogController
{
    protected string $modelClass = Jurisdiccion::class;

    protected array $fillable = [
        'tipo_jurisdiccion_id',
        'jurisdiccion_padre_id',
        'nombre',
        'codigo_oficial',
        'latitud',
        'longitud',
        'activa',
    ];

    protected array $filterable = [
        'tipo_jurisdiccion_id',
        'jurisdiccion_padre_id',
        'nombre',
        'activa',
    ];

    protected array $booleanFilters = ['activa'];

    protected array $with = ['tipoJurisdiccion', 'jurisdiccionPadre'];

    protected string $defaultSort = 'nombre';

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->storeRules());

        $item = $this->modelClass::create($this->onlyFillable($data));

        return response()->json($this->freshItem($item), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = $this->findItem($id);
        $data = $request->validate($this->updateRules($item->id));

        $item->update($this->onlyFillable($data));

        return response()->json($this->freshItem($item));
    }

    private function storeRules(): array
    {
        return [
            'tipo_jurisdiccion_id' => 'required|integer|exists:tipos_jurisdiccion,id',
            'jurisdiccion_padre_id' => 'nullable|integer|exists:jurisdicciones,id',
            'nombre' => 'required|string|max:220',
            'codigo_oficial' => 'nullable|string|max:80',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activa' => 'nullable|boolean',
        ];
    }

    private function updateRules(int $ignoreId): array
    {
        return [
            'tipo_jurisdiccion_id' => 'sometimes|required|integer|exists:tipos_jurisdiccion,id',
            'jurisdiccion_padre_id' => [
                'nullable',
                'integer',
                'exists:jurisdicciones,id',
                function (string $attribute, mixed $value, \Closure $fail) use ($ignoreId): void {
                    if ($ignoreId !== null && (int) $value === $ignoreId) {
                        $fail('La jurisdiccion no puede ser su propio padre.');
                    }
                },
            ],
            'nombre' => 'sometimes|required|string|max:220',
            'codigo_oficial' => 'nullable|string|max:80',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'activa' => 'nullable|boolean',
        ];
    }
}
