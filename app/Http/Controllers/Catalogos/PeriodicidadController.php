<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\Periodicidad;

class PeriodicidadController extends BaseCatalogController
{
    protected string $modelClass = Periodicidad::class;

    protected array $fillable = ['codigo', 'nombre', 'descripcion'];

    protected array $storeRules = [
        'codigo' => 'required|string|max:40|unique:periodicidades,codigo',
        'nombre' => 'required|string|max:80|unique:periodicidades,nombre',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $updateRules = [
        'codigo' => 'sometimes|required|string|max:40|unique:periodicidades,codigo,{id}',
        'nombre' => 'sometimes|required|string|max:80|unique:periodicidades,nombre,{id}',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $filterable = ['codigo', 'nombre'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['codigo'] = 'sometimes|required|string|max:40|unique:periodicidades,codigo,'.$id;
        $this->updateRules['nombre'] = 'sometimes|required|string|max:80|unique:periodicidades,nombre,'.$id;

        return parent::update($request, $id);
    }
}
