<?php

namespace App\Http\Controllers\DatosFuente;

use App\Http\Controllers\Controller;
use App\Models\DatoFuente;
use App\Models\DatoFuenteValor;
use App\Models\EstadoDato;
use App\Support\Observability\Observability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DatoFuenteValorController extends Controller
{
    public function index(Request $request, string $datoFuenteId): JsonResponse
    {
        $datoFuente = DatoFuente::findOrFail($datoFuenteId);
        $query = $datoFuente->valores()->with($this->relations());

        foreach ([
            'jurisdiccion_id',
            'estado_dato_id',
            'modalidad_carga_id',
            'usuario_carga_id',
            'usuario_valida_id',
            'periodo_referencia',
            'fecha_produccion',
        ] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('vigente')) {
            $query->where('vigente', filter_var($request->input('vigente'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json(
            $query->orderByDesc('periodo_referencia')->orderByDesc('fecha_carga')->paginate($perPage)
        );
    }

    public function store(Request $request, string $datoFuenteId): JsonResponse
    {
        $datoFuente = DatoFuente::findOrFail($datoFuenteId);
        $data = $request->validate($this->storeRules());

        $this->ensureObservationReason($data['estado_dato_id'] ?? null, $data['observado_motivo'] ?? null);
        $this->ensureRange($datoFuente, $data['valor_crudo'] ?? null, 'valor_crudo');
        $this->ensureRange($datoFuente, $data['valor_utilizado'] ?? null, 'valor_utilizado');

        $payload = $this->payload($data);
        $payload['usuario_carga_id'] = $request->user()->id;
        $payload['fecha_carga'] = now();

        $valor = $datoFuente->valores()->create($payload);
        Observability::audit($request, 'datos_fuente_valores', $valor->id, 'CREAR', null, $valor);

        return response()->json($this->loadValor($valor), 201);
    }

    public function show(string $datoFuenteId, string $id): JsonResponse
    {
        return response()->json($this->findValor($datoFuenteId, $id));
    }

    public function update(Request $request, string $datoFuenteId, string $id): JsonResponse
    {
        $valor = $this->findValor($datoFuenteId, $id);
        $before = $valor->withoutRelations()->toArray();
        $data = $request->validate($this->updateRules());

        $this->ensureObservationReason($data['estado_dato_id'] ?? null, $data['observado_motivo'] ?? $valor->observado_motivo);
        $this->ensureRange($valor->datoFuente, $data['valor_crudo'] ?? null, 'valor_crudo');
        $this->ensureRange($valor->datoFuente, $data['valor_utilizado'] ?? null, 'valor_utilizado');

        $valor->update($this->payload($data));
        Observability::audit($request, 'datos_fuente_valores', $valor->id, 'ACTUALIZAR', $before, $valor->fresh());

        return response()->json($this->loadValor($valor));
    }

    public function validar(Request $request, string $datoFuenteId, string $id): JsonResponse
    {
        $valor = $this->findValor($datoFuenteId, $id);
        $before = $valor->withoutRelations()->toArray();
        $data = $request->validate([
            'estado_dato_id' => 'required|integer|exists:estados_dato,id',
            'valor_utilizado' => 'nullable|numeric',
            'observado_motivo' => 'nullable|string|max:700',
            'vigente' => 'nullable|boolean',
        ]);

        $estado = EstadoDato::findOrFail($data['estado_dato_id']);
        $valorUtilizado = $data['valor_utilizado'] ?? $valor->valor_utilizado ?? $valor->valor_crudo;

        $this->ensureRange($valor->datoFuente, $valorUtilizado, 'valor_utilizado');

        $this->ensureObservationReason($estado->id, $data['observado_motivo'] ?? null);

        $valor->update([
            'estado_dato_id' => $estado->id,
            'valor_utilizado' => $valorUtilizado,
            'observado_motivo' => $data['observado_motivo'] ?? null,
            'vigente' => $data['vigente'] ?? $valor->vigente,
            'usuario_valida_id' => $request->user()->id,
            'validado_at' => now(),
        ]);

        Observability::audit(
            $request,
            'datos_fuente_valores',
            $valor->id,
            'VALIDAR',
            $before,
            $valor->fresh(),
            $data['observado_motivo'] ?? null
        );

        if (in_array($estado->codigo, ['OBSERVADO', 'RECHAZADO'], true)) {
            $destinatario = $valor->usuarioCarga;

            Observability::alertWithInternalNotification(
                [
                    'tipo_alerta' => 'DATO_FUENTE_'.$estado->codigo,
                    'severidad' => $estado->codigo === 'RECHAZADO' ? 'ALTA' : 'MEDIA',
                    'titulo' => 'Dato fuente '.$estado->nombre,
                    'mensaje' => 'El valor cargado para "'.$valor->datoFuente->nombre.'" quedo en estado '.$estado->nombre.'.',
                    'entidad_tipo' => 'datos_fuente_valores',
                    'entidad_id' => $valor->id,
                ],
                $destinatario,
                'Revision de dato fuente',
                'El valor del dato fuente "'.$valor->datoFuente->nombre.'" fue marcado como '.$estado->nombre.'. Motivo: '.($data['observado_motivo'] ?? 'Sin detalle adicional.')
            );
        }

        return response()->json($this->loadValor($valor));
    }

    public function destroy(string $datoFuenteId, string $id): JsonResponse
    {
        $valor = $this->findValor($datoFuenteId, $id);
        $before = $valor->withoutRelations()->toArray();
        $valor->delete();
        Observability::audit(request(), 'datos_fuente_valores', $valor->id, 'ELIMINAR', $before, null);

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'jurisdiccion_id' => 'required|integer|exists:jurisdicciones,id',
            'estado_dato_id' => 'required|integer|exists:estados_dato,id',
            'modalidad_carga_id' => 'required|integer|exists:modalidades_carga,id',
            'valor_crudo' => 'required|numeric',
            'valor_utilizado' => 'nullable|numeric',
            'periodo_referencia' => 'required|date',
            'fecha_produccion' => 'nullable|date',
            'observado_motivo' => 'nullable|string|max:700',
            'vigente' => 'nullable|boolean',
        ];
    }

    private function updateRules(): array
    {
        return [
            'jurisdiccion_id' => 'sometimes|required|integer|exists:jurisdicciones,id',
            'estado_dato_id' => 'sometimes|required|integer|exists:estados_dato,id',
            'modalidad_carga_id' => 'sometimes|required|integer|exists:modalidades_carga,id',
            'valor_crudo' => 'sometimes|required|numeric',
            'valor_utilizado' => 'nullable|numeric',
            'periodo_referencia' => 'sometimes|required|date',
            'fecha_produccion' => 'nullable|date',
            'observado_motivo' => 'nullable|string|max:700',
            'vigente' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'jurisdiccion_id',
            'estado_dato_id',
            'modalidad_carga_id',
            'valor_crudo',
            'valor_utilizado',
            'periodo_referencia',
            'fecha_produccion',
            'observado_motivo',
            'vigente',
        ])->all();
    }

    private function findValor(string $datoFuenteId, string $id): DatoFuenteValor
    {
        return DatoFuenteValor::query()
            ->where('dato_fuente_id', $datoFuenteId)
            ->with($this->relations())
            ->findOrFail($id);
    }

    private function loadValor(DatoFuenteValor $valor): DatoFuenteValor
    {
        return $valor->fresh($this->relations());
    }

    private function ensureRange(DatoFuente $datoFuente, mixed $value, string $field): void
    {
        if ($value === null) {
            return;
        }

        if ($datoFuente->rango_minimo !== null && $value < $datoFuente->rango_minimo) {
            throw ValidationException::withMessages([
                $field => ['El valor esta por debajo del rango minimo configurado para el dato fuente.'],
            ]);
        }

        if ($datoFuente->rango_maximo !== null && $value > $datoFuente->rango_maximo) {
            throw ValidationException::withMessages([
                $field => ['El valor supera el rango maximo configurado para el dato fuente.'],
            ]);
        }
    }

    private function ensureObservationReason(?int $estadoDatoId, ?string $motivo): void
    {
        if ($estadoDatoId === null) {
            return;
        }

        $estado = EstadoDato::find($estadoDatoId);

        if ($estado && in_array($estado->codigo, ['OBSERVADO', 'RECHAZADO'], true) && blank($motivo)) {
            throw ValidationException::withMessages([
                'observado_motivo' => ['El motivo es obligatorio cuando el dato queda observado o rechazado.'],
            ]);
        }
    }

    private function relations(): array
    {
        return [
            'datoFuente',
            'jurisdiccion',
            'estadoDato',
            'modalidadCarga',
            'usuarioCarga',
            'usuarioValida',
            'evidencias',
        ];
    }
}
