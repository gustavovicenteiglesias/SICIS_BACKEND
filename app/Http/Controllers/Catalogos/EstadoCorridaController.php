<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\EstadoCorrida;

class EstadoCorridaController extends BaseCatalogController
{
    protected string $modelClass = EstadoCorrida::class;

    protected array $fillable = ['codigo', 'nombre', 'descripcion'];

    protected array $storeRules = [
        'codigo' => 'required|string|max:50|unique:estados_corrida,codigo',
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $updateRules = [
        'codigo' => 'sometimes|required|string|max:50|unique:estados_corrida,codigo,{id}',
        'nombre' => 'sometimes|required|string|max:100',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $filterable = ['codigo', 'nombre'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['codigo'] = 'sometimes|required|string|max:50|unique:estados_corrida,codigo,'.$id;

        return parent::update($request, $id);
    }
}
