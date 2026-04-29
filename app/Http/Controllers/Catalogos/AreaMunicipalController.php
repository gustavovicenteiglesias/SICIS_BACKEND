<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\AreaMunicipal;

class AreaMunicipalController extends BaseCatalogController
{
    protected string $modelClass = AreaMunicipal::class;

    protected array $fillable = ['nombre', 'descripcion', 'activa'];

    protected array $storeRules = [
        'nombre' => 'required|string|max:180|unique:areas_municipales,nombre',
        'descripcion' => 'nullable|string|max:255',
        'activa' => 'nullable|boolean',
    ];

    protected array $updateRules = [
        'nombre' => 'sometimes|required|string|max:180|unique:areas_municipales,nombre,{id}',
        'descripcion' => 'nullable|string|max:255',
        'activa' => 'sometimes|required|boolean',
    ];

    protected array $filterable = ['nombre', 'activa'];

    protected array $booleanFilters = ['activa'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['nombre'] = 'sometimes|required|string|max:180|unique:areas_municipales,nombre,'.$id;

        return parent::update($request, $id);
    }
}
