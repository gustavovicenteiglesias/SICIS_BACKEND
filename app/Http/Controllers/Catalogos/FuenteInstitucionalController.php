<?php

namespace App\Http\Controllers\Catalogos;

use App\Models\FuenteInstitucional;

class FuenteInstitucionalController extends BaseCatalogController
{
    protected string $modelClass = FuenteInstitucional::class;

    protected array $fillable = [
        'nombre',
        'organismo',
        'descripcion',
        'url_base',
        'responsable',
        'contacto',
        'activa',
    ];

    protected array $storeRules = [
        'nombre' => 'required|string|max:220|unique:fuentes_institucionales,nombre',
        'organismo' => 'nullable|string|max:220',
        'descripcion' => 'nullable|string|max:500',
        'url_base' => 'nullable|string|max:500',
        'responsable' => 'nullable|string|max:255',
        'contacto' => 'nullable|string|max:255',
        'activa' => 'nullable|boolean',
    ];

    protected array $updateRules = [
        'nombre' => 'sometimes|required|string|max:220|unique:fuentes_institucionales,nombre,{id}',
        'organismo' => 'nullable|string|max:220',
        'descripcion' => 'nullable|string|max:500',
        'url_base' => 'nullable|string|max:500',
        'responsable' => 'nullable|string|max:255',
        'contacto' => 'nullable|string|max:255',
        'activa' => 'sometimes|required|boolean',
    ];

    protected array $filterable = ['nombre', 'organismo', 'activa'];

    protected array $booleanFilters = ['activa'];

    protected string $defaultSort = 'nombre';

    public function update(\Illuminate\Http\Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $this->updateRules['nombre'] = 'sometimes|required|string|max:220|unique:fuentes_institucionales,nombre,'.$id;

        return parent::update($request, $id);
    }
}
