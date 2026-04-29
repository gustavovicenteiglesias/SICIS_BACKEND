<?php

namespace App\Http\Controllers\DatosFuente;

use App\Http\Controllers\Controller;
use App\Models\DatoFuenteValor;
use App\Models\EvidenciaDato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvidenciaDatoController extends Controller
{
    public function index(Request $request, string $datoFuenteId, string $valorId): JsonResponse
    {
        $valor = $this->findValor($datoFuenteId, $valorId);
        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json(
            $valor->evidencias()->with('usuario')->latest()->paginate($perPage)
        );
    }

    public function store(Request $request, string $datoFuenteId, string $valorId): JsonResponse
    {
        $valor = $this->findValor($datoFuenteId, $valorId);
        $data = $request->validate($this->storeRules());

        $evidencia = $valor->evidencias()->create([
            ...$this->payload($data),
            'usuario_id' => $request->user()->id,
        ]);

        return response()->json($this->loadEvidencia($evidencia), 201);
    }

    public function show(string $datoFuenteId, string $valorId, string $id): JsonResponse
    {
        return response()->json($this->findEvidencia($datoFuenteId, $valorId, $id));
    }

    public function update(Request $request, string $datoFuenteId, string $valorId, string $id): JsonResponse
    {
        $evidencia = $this->findEvidencia($datoFuenteId, $valorId, $id);
        $data = $request->validate($this->updateRules());

        $evidencia->update($this->payload($data));

        return response()->json($this->loadEvidencia($evidencia));
    }

    public function destroy(string $datoFuenteId, string $valorId, string $id): JsonResponse
    {
        $evidencia = $this->findEvidencia($datoFuenteId, $valorId, $id);
        $evidencia->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'nombre_archivo' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
            'hash_archivo' => 'nullable|string|max:128',
            'descripcion' => 'nullable|string|max:700',
        ];
    }

    private function updateRules(): array
    {
        return [
            'nombre_archivo' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:500',
            'hash_archivo' => 'nullable|string|max:128',
            'descripcion' => 'nullable|string|max:700',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'nombre_archivo',
            'url',
            'hash_archivo',
            'descripcion',
        ])->all();
    }

    private function findValor(string $datoFuenteId, string $valorId): DatoFuenteValor
    {
        return DatoFuenteValor::query()
            ->where('dato_fuente_id', $datoFuenteId)
            ->findOrFail($valorId);
    }

    private function findEvidencia(string $datoFuenteId, string $valorId, string $id): EvidenciaDato
    {
        $valor = $this->findValor($datoFuenteId, $valorId);

        return $valor->evidencias()->with('usuario')->findOrFail($id);
    }

    private function loadEvidencia(EvidenciaDato $evidencia): EvidenciaDato
    {
        return $evidencia->fresh('usuario');
    }
}
