<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\UnidadMedida;

class UnidadMedidaController extends BaseCatalogController
{
    protected string $modelClass = UnidadMedida::class;

    protected array $fillable = ['nombre', 'simbolo', 'descripcion'];

    protected array $storeRules = [
        'nombre' => 'required|string|max:120|unique:unidades_medida,nombre',
        'simbolo' => 'nullable|string|max:50',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $updateRules = [
        'nombre' => 'sometimes|required|string|max:120|unique:unidades_medida,nombre,{id}',
        'simbolo' => 'nullable|string|max:50',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $filterable = ['nombre'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['nombre'] = 'sometimes|required|string|max:120|unique:unidades_medida,nombre,'.$id;

        return parent::update($request, $id);
    }
}
