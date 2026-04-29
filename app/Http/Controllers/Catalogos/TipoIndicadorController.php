<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\TipoIndicador;

class TipoIndicadorController extends BaseCatalogController
{
    protected string $modelClass = TipoIndicador::class;

    protected array $fillable = ['codigo', 'nombre', 'descripcion'];

    protected array $storeRules = [
        'codigo' => 'required|string|max:40|unique:tipos_indicador,codigo',
        'nombre' => 'required|string|max:120',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $updateRules = [
        'codigo' => 'sometimes|required|string|max:40|unique:tipos_indicador,codigo,{id}',
        'nombre' => 'sometimes|required|string|max:120',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $filterable = ['codigo', 'nombre'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['codigo'] = 'sometimes|required|string|max:40|unique:tipos_indicador,codigo,'.$id;

        return parent::update($request, $id);
    }
}
