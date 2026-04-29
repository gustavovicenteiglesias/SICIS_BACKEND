<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\CategoriaTematica;
use Illuminate\Validation\Rule;

class CategoriaTematicaController extends BaseCatalogController
{
    protected string $modelClass = CategoriaTematica::class;

    protected array $fillable = ['categoria_id', 'nombre', 'descripcion', 'orden', 'activa'];

    protected array $storeRules = [
        'categoria_id' => 'required|integer|exists:categorias,id',
        'nombre' => 'required|string|max:180',
        'descripcion' => 'nullable|string|max:255',
        'orden' => 'nullable|integer|min:1',
        'activa' => 'nullable|boolean',
    ];

    protected array $updateRules = [
        'categoria_id' => 'sometimes|required|integer|exists:categorias,id',
        'nombre' => 'sometimes|required|string|max:180',
        'descripcion' => 'nullable|string|max:255',
        'orden' => 'sometimes|required|integer|min:1',
        'activa' => 'sometimes|required|boolean',
    ];

    protected array $filterable = ['categoria_id', 'nombre', 'activa'];

    protected array $booleanFilters = ['activa'];

    protected array $with = ['categoria'];

    protected string $defaultSort = 'orden';

    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'nombre' => [
                'required',
                'string',
                'max:180',
                Rule::unique('categorias_tematicas', 'nombre')->where(
                    fn ($query) => $query->where('categoria_id', $request->integer('categoria_id'))
                ),
            ],
            'descripcion' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:1',
            'activa' => 'nullable|boolean',
        ]);

        $item = $this->modelClass::create($this->onlyFillable($data));

        return response()->json($this->freshItem($item), 201);
    }

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $item = $this->findItem($id);
        $categoriaId = (int) $request->input('categoria_id', $item->categoria_id);

        $data = $request->validate([
            'categoria_id' => ['sometimes', 'required', 'integer', 'exists:categorias,id'],
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:180',
                Rule::unique('categorias_tematicas', 'nombre')
                    ->ignore($id)
                    ->where(fn ($query) => $query->where('categoria_id', $categoriaId)),
            ],
            'descripcion' => 'nullable|string|max:255',
            'orden' => 'sometimes|required|integer|min:1',
            'activa' => 'sometimes|required|boolean',
        ]);

        $item->update($this->onlyFillable($data));

        return response()->json($this->freshItem($item));
    }
}
