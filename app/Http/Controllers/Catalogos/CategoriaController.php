<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Categoria;

class CategoriaController extends BaseCatalogController
{
    protected string $modelClass = Categoria::class;

    protected array $fillable = ['nombre', 'descripcion', 'orden', 'activa'];

    protected array $storeRules = [
        'nombre' => 'required|string|max:180|unique:categorias,nombre',
        'descripcion' => 'nullable|string|max:255',
        'orden' => 'nullable|integer|min:1',
        'activa' => 'nullable|boolean',
    ];

    protected array $updateRules = [
        'nombre' => 'sometimes|required|string|max:180|unique:categorias,nombre,{id}',
        'descripcion' => 'nullable|string|max:255',
        'orden' => 'sometimes|required|integer|min:1',
        'activa' => 'sometimes|required|boolean',
    ];

    protected array $filterable = ['nombre', 'activa'];

    protected array $booleanFilters = ['activa'];

    protected string $defaultSort = 'orden';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['nombre'] = 'sometimes|required|string|max:180|unique:categorias,nombre,'.$id;

        return parent::update($request, $id);
    }
}
