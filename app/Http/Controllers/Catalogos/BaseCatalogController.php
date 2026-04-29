<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseCatalogController extends Controller
{
    protected string $modelClass;

    protected array $fillable = [];

    protected array $storeRules = [];

    protected array $updateRules = [];

    protected array $filterable = [];

    protected array $booleanFilters = [];

    protected array $with = [];

    protected string $defaultSort = 'id';

    public function index(Request $request): JsonResponse
    {
        $query = $this->newQuery();

        foreach ($this->filterable as $column) {
            if (!$request->filled($column)) {
                continue;
            }

            $value = $request->input($column);

            if (in_array($column, $this->booleanFilters, true)) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if ($value === null) {
                    continue;
                }
            }

            $query->where($column, $value);
        }

        $perPage = (int) $request->integer('per_page', 15);
        $perPage = max(1, min($perPage, 100));

        return response()->json(
            $query->orderBy($this->defaultSort)->paginate($perPage)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->storeRules);

        $item = $this->modelClass::create($this->onlyFillable($data));

        return response()->json($this->freshItem($item), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->findItem($id));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = $this->findItem($id);
        $data = $request->validate($this->updateRules);

        $item->update($this->onlyFillable($data));

        return response()->json($this->freshItem($item));
    }

    public function destroy(string $id): JsonResponse
    {
        $item = $this->findItem($id);
        $item->delete();

        return response()->json([], 204);
    }

    protected function newQuery(): Builder
    {
        /** @var Model $model */
        $model = new $this->modelClass();

        return $model->newQuery()->with($this->with);
    }

    protected function findItem(string $id): Model
    {
        $query = $this->newQuery();

        if ($this->usesSoftDeletes()) {
            $query->whereNull($query->getModel()->getQualifiedDeletedAtColumn());
        }

        return $query->findOrFail($id);
    }

    protected function freshItem(Model $item): Model
    {
        return $item->fresh($this->with);
    }

    protected function onlyFillable(array $data): array
    {
        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->modelClass), true);
    }
}
