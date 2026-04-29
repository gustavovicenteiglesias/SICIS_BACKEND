<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\ModalidadCarga;

class ModalidadCargaController extends BaseCatalogController
{
    protected string $modelClass = ModalidadCarga::class;

    protected array $fillable = ['codigo', 'nombre', 'descripcion'];

    protected array $storeRules = [
        'codigo' => 'required|string|max:50|unique:modalidades_carga,codigo',
        'nombre' => 'required|string|max:80',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $updateRules = [
        'codigo' => 'sometimes|required|string|max:50|unique:modalidades_carga,codigo,{id}',
        'nombre' => 'sometimes|required|string|max:80',
        'descripcion' => 'nullable|string|max:255',
    ];

    protected array $filterable = ['codigo', 'nombre'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['codigo'] = 'sometimes|required|string|max:50|unique:modalidades_carga,codigo,'.$id;

        return parent::update($request, $id);
    }
}
