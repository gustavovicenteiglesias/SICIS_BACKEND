<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Norma;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NormaController extends BaseCatalogController
{
    protected string $modelClass = Norma::class;

    protected array $fillable = ['codigo', 'nombre', 'version', 'anio', 'descripcion', 'activa'];

    protected array $filterable = ['codigo', 'nombre', 'activa'];

    protected array $booleanFilters = ['activa'];

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
            'codigo' => ['required', 'string', 'max:40'],
            'nombre' => ['required', 'string', 'max:250'],
            'version' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('normas')
                    ->where(fn ($query) => $query->where('codigo', request('codigo', ''))),
            ],
            'anio' => 'nullable|integer|min:1900|max:2100',
            'descripcion' => 'nullable|string|max:500',
            'activa' => 'nullable|boolean',
        ];
    }

    private function updateRules(int $ignoreId): array
    {
        return [
            'codigo' => ['sometimes', 'required', 'string', 'max:40'],
            'nombre' => ['sometimes', 'required', 'string', 'max:250'],
            'version' => [
                'nullable',
                'string',
                'max:40',
                Rule::unique('normas')
                    ->ignore($ignoreId)
                    ->where(fn ($query) => $query->where('codigo', request('codigo', ''))),
            ],
            'anio' => 'nullable|integer|min:1900|max:2100',
            'descripcion' => 'nullable|string|max:500',
            'activa' => 'nullable|boolean',
        ];
    }
}
