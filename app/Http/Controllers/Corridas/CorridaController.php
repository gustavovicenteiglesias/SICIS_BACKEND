<?php

namespace App\Http\Controllers\Corridas;

use App\Http\Controllers\Controller;
use App\Models\Corrida;
use App\Models\CorridaSnapshotDato;
use App\Models\CorridaSnapshotIndicador;
use App\Models\DatoFuenteValor;
use App\Models\EstadoCorrida;
use App\Models\EstadoResultado;
use App\Models\IndicadorVersion;
use App\Support\Observability\Observability;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CorridaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Corrida::query()->with($this->indexRelations());

        foreach ([
            'jurisdiccion_id',
            'estado_corrida_id',
            'periodo_referencia',
            'usuario_ejecucion_id',
            'usuario_aprobacion_id',
        ] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json(
            $query->orderByDesc('periodo_referencia')->orderByDesc('id')->paginate($perPage)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->storeRules());
        $estadoBorrador = $this->estadoCorrida('BORRADOR');

        $corrida = Corrida::create([
            'jurisdiccion_id' => $data['jurisdiccion_id'],
            'estado_corrida_id' => $estadoBorrador->id,
            'titulo' => $data['titulo'],
            'periodo_referencia' => $data['periodo_referencia'],
            'usuario_ejecucion_id' => $request->user()->id,
            'observaciones' => $data['observaciones'] ?? null,
        ]);

        Observability::audit($request, 'corridas', $corrida->id, 'CREAR', null, $corrida);

        return response()->json($this->loadCorrida($corrida), 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json($this->loadCorrida(Corrida::findOrFail($id)));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $corrida = Corrida::findOrFail($id);
        $this->ensureEditable($corrida);
        $before = $corrida->withoutRelations()->toArray();

        $data = $request->validate($this->updateRules());

        $corrida->update(collect($data)->only([
            'jurisdiccion_id',
            'titulo',
            'periodo_referencia',
            'observaciones',
        ])->all());

        Observability::audit($request, 'corridas', $corrida->id, 'ACTUALIZAR', $before, $corrida->fresh());

        return response()->json($this->loadCorrida($corrida));
    }

    public function ejecutar(Request $request, string $id): JsonResponse
    {
        $corrida = Corrida::findOrFail($id);
        $before = $corrida->withoutRelations()->toArray();
        $data = $request->validate([
            'observaciones' => 'nullable|string|max:700',
        ]);

        $estadoEjecutada = $this->estadoCorrida('EJECUTADA');
        $estadoObservada = $this->estadoCorrida('OBSERVADA');
        $estadoCalculado = $this->estadoResultado('CALCULADO');
        $estadoSinDatos = $this->estadoResultado('SIN_DATOS');
        $estadoError = $this->estadoResultado('ERROR_CALCULO');

        try {
            DB::transaction(function () use ($corrida, $request, $data, $estadoEjecutada, $estadoCalculado, $estadoSinDatos, $estadoError): void {
                $corrida->snapshotDatos()->delete();
                $corrida->snapshotIndicadores()->delete();

                $versions = $this->versionsForCorrida($corrida);
                $usedData = [];

                foreach ($versions as $version) {
                    $calculo = $this->calcularIndicador($corrida, $version);

                    foreach ($calculo['used_values'] as $datoFuenteValor) {
                        $usedData[$datoFuenteValor->dato_fuente_id] = $datoFuenteValor;
                    }

                    $estadoResultadoId = match ($calculo['status']) {
                        'CALCULADO' => $estadoCalculado->id,
                        'SIN_DATOS' => $estadoSinDatos->id,
                        default => $estadoError->id,
                    };

                    CorridaSnapshotIndicador::create([
                        'corrida_id' => $corrida->id,
                        'indicador_id' => $version->indicador_id,
                        'indicador_version_id' => $version->id,
                        'estado_resultado_id' => $estadoResultadoId,
                        'unidad_medida_id' => $version->unidad_medida_id,
                        'valor_resultado' => $calculo['value'],
                        'formula_tipo' => $version->formula_tipo,
                        'constante' => $version->constante,
                        'formula_texto_usada' => $version->formula_texto,
                        'formula_expression_usada' => $version->formula_expression,
                        'publicable_en_corrida' => (bool) $version->indicador->publicable,
                        'periodo_referencia' => $corrida->periodo_referencia,
                        'observaciones' => $calculo['message'],
                    ]);
                }

                foreach ($usedData as $datoFuenteValor) {
                    CorridaSnapshotDato::create([
                        'corrida_id' => $corrida->id,
                        'dato_fuente_id' => $datoFuenteValor->dato_fuente_id,
                        'dato_fuente_valor_id' => $datoFuenteValor->id,
                        'unidad_medida_id' => $datoFuenteValor->datoFuente->unidad_medida_id,
                        'valor_dato' => $datoFuenteValor->valor_utilizado ?? $datoFuenteValor->valor_crudo,
                        'periodo_referencia' => $datoFuenteValor->periodo_referencia,
                    ]);
                }

                $corrida->update([
                    'estado_corrida_id' => $estadoEjecutada->id,
                    'usuario_ejecucion_id' => $request->user()->id,
                    'ejecutada_at' => now(),
                    'observaciones' => $data['observaciones'] ?? $corrida->observaciones,
                ]);
            });
        } catch (\Throwable $e) {
            $corrida->update([
                'estado_corrida_id' => $estadoObservada->id,
                'observaciones' => $e->getMessage(),
            ]);

            Observability::audit($request, 'corridas', $corrida->id, 'EJECUTAR_ERROR', $before, $corrida->fresh(), $e->getMessage());
            Observability::alertWithInternalNotification(
                [
                    'tipo_alerta' => 'CORRIDA_ERROR',
                    'severidad' => 'ALTA',
                    'titulo' => 'Fallo en ejecucion de corrida',
                    'mensaje' => 'La corrida "'.$corrida->titulo.'" no pudo ejecutarse correctamente.',
                    'entidad_tipo' => 'corridas',
                    'entidad_id' => $corrida->id,
                ],
                $request->user(),
                'Corrida observada por error',
                'La corrida "'.$corrida->titulo.'" entro en estado observada por el siguiente motivo: '.$e->getMessage()
            );

            throw ValidationException::withMessages([
                'corrida' => ['La corrida no pudo ejecutarse correctamente: '.$e->getMessage()],
            ]);
        }

        Observability::audit($request, 'corridas', $corrida->id, 'EJECUTAR', $before, $corrida->fresh());

        $errores = $corrida->fresh('snapshotIndicadores.estadoResultado')
            ->snapshotIndicadores
            ->whereIn('estadoResultado.codigo', ['SIN_DATOS', 'ERROR_CALCULO'])
            ->count();

        if ($errores > 0) {
            Observability::alertWithInternalNotification(
                [
                    'tipo_alerta' => 'CORRIDA_CON_OBSERVACIONES',
                    'severidad' => 'MEDIA',
                    'titulo' => 'Corrida ejecutada con observaciones',
                    'mensaje' => 'La corrida "'.$corrida->titulo.'" finalizo con '.$errores.' resultado(s) no calculado(s) o con error.',
                    'entidad_tipo' => 'corridas',
                    'entidad_id' => $corrida->id,
                ],
                $request->user()
            );
        }

        return response()->json($this->loadCorrida($corrida->fresh()));
    }

    public function aprobar(Request $request, string $id): JsonResponse
    {
        $corrida = Corrida::findOrFail($id);
        $before = $corrida->withoutRelations()->toArray();
        $data = $request->validate([
            'observaciones' => 'nullable|string|max:700',
        ]);

        $estadoActual = $this->estadoCorridaById($corrida->estado_corrida_id)?->codigo;

        if ($estadoActual !== 'EJECUTADA') {
            throw ValidationException::withMessages([
                'corrida' => ['Solo se pueden aprobar corridas en estado EJECUTADA.'],
            ]);
        }

        $corrida->update([
            'estado_corrida_id' => $this->estadoCorrida('APROBADA')->id,
            'usuario_aprobacion_id' => $request->user()->id,
            'aprobada_at' => now(),
            'observaciones' => $data['observaciones'] ?? $corrida->observaciones,
        ]);

        Observability::audit($request, 'corridas', $corrida->id, 'APROBAR', $before, $corrida->fresh(), $data['observaciones'] ?? null);

        return response()->json($this->loadCorrida($corrida));
    }

    public function publicar(Request $request, string $id): JsonResponse
    {
        $corrida = Corrida::findOrFail($id);
        $before = $corrida->withoutRelations()->toArray();
        $data = $request->validate([
            'observaciones' => 'nullable|string|max:700',
        ]);

        $estadoActual = $this->estadoCorridaById($corrida->estado_corrida_id)?->codigo;

        if ($estadoActual !== 'APROBADA') {
            throw ValidationException::withMessages([
                'corrida' => ['Solo se pueden publicar corridas en estado APROBADA.'],
            ]);
        }

        $corrida->update([
            'estado_corrida_id' => $this->estadoCorrida('PUBLICADA')->id,
            'publicada_at' => now(),
            'observaciones' => $data['observaciones'] ?? $corrida->observaciones,
        ]);

        Observability::audit($request, 'corridas', $corrida->id, 'PUBLICAR', $before, $corrida->fresh(), $data['observaciones'] ?? null);

        return response()->json($this->loadCorrida($corrida));
    }

    private function storeRules(): array
    {
        return [
            'jurisdiccion_id' => 'required|integer|exists:jurisdicciones,id',
            'titulo' => 'required|string|max:250',
            'periodo_referencia' => 'required|date',
            'observaciones' => 'nullable|string|max:700',
        ];
    }

    private function updateRules(): array
    {
        return [
            'jurisdiccion_id' => 'sometimes|required|integer|exists:jurisdicciones,id',
            'titulo' => 'sometimes|required|string|max:250',
            'periodo_referencia' => 'sometimes|required|date',
            'observaciones' => 'nullable|string|max:700',
        ];
    }

    private function loadCorrida(Corrida $corrida): Corrida
    {
        return $corrida->fresh($this->showRelations());
    }

    private function indexRelations(): array
    {
        return [
            'jurisdiccion',
            'estadoCorrida',
            'usuarioEjecucion',
            'usuarioAprobacion',
        ];
    }

    private function showRelations(): array
    {
        return [
            'jurisdiccion',
            'estadoCorrida',
            'usuarioEjecucion',
            'usuarioAprobacion',
            'snapshotDatos.datoFuente',
            'snapshotDatos.datoFuenteValor',
            'snapshotDatos.unidadMedida',
            'snapshotIndicadores.indicador',
            'snapshotIndicadores.indicadorVersion',
            'snapshotIndicadores.estadoResultado',
            'snapshotIndicadores.unidadMedida',
        ];
    }

    private function ensureEditable(Corrida $corrida): void
    {
        $codigo = $corrida->estadoCorrida?->codigo ?? $this->estadoCorridaById($corrida->estado_corrida_id)?->codigo;

        if (!in_array($codigo, ['BORRADOR', 'OBSERVADA'], true)) {
            throw ValidationException::withMessages([
                'corrida' => ['Solo se pueden editar corridas en estado BORRADOR u OBSERVADA.'],
            ]);
        }
    }

    private function versionsForCorrida(Corrida $corrida): Collection
    {
        return IndicadorVersion::query()
            ->where('activa', true)
            ->whereDate('vigente_desde', '<=', $corrida->periodo_referencia)
            ->where(function ($query) use ($corrida): void {
                $query->whereNull('vigente_hasta')
                    ->orWhereDate('vigente_hasta', '>=', $corrida->periodo_referencia);
            })
            ->whereHas('indicador', fn ($query) => $query->where('activo', true))
            ->with(['indicador', 'variables.datoFuente', 'unidadMedida'])
            ->get()
            ->sortByDesc('vigente_desde')
            ->groupBy('indicador_id')
            ->map(fn (Collection $group) => $group->first())
            ->values();
    }

    private function calcularIndicador(Corrida $corrida, IndicadorVersion $version): array
    {
        $usedValues = collect();
        $valuesByRole = [];
        $resolvedValues = [];

        foreach ($version->variables->sortBy('orden') as $variable) {
            $valor = DatoFuenteValor::query()
                ->where('dato_fuente_id', $variable->dato_fuente_id)
                ->where('jurisdiccion_id', $corrida->jurisdiccion_id)
                ->whereDate('periodo_referencia', $corrida->periodo_referencia)
                ->where('vigente', true)
                ->whereHas('estadoDato', fn ($query) => $query->where('codigo', 'VALIDADO'))
                ->latest('validado_at')
                ->latest('id')
                ->with('datoFuente')
                ->first();

            if (!$valor && $variable->obligatorio) {
                return [
                    'status' => 'SIN_DATOS',
                    'value' => null,
                    'message' => 'Faltan datos validados para la variable '.$variable->codigo_variable.'.',
                    'used_values' => $usedValues,
                ];
            }

            if (!$valor) {
                continue;
            }

            $usedValues->push($valor);
            $numericValue = (float) ($valor->valor_utilizado ?? $valor->valor_crudo);
            $valuesByRole[strtoupper($variable->rol)][] = $numericValue;
            $valuesByRole[strtoupper($variable->codigo_variable)][] = $numericValue;
            $resolvedValues[] = $numericValue;
        }

        $constante = (float) ($version->constante ?? 1);
        $formulaTipo = strtoupper((string) $version->formula_tipo);

        if ($formulaTipo === 'RATIO_CONSTANTE' || isset($valuesByRole['DENOMINADOR']) || isset($valuesByRole['B'])) {
            $numerador = array_sum($valuesByRole['NUMERADOR'] ?? $valuesByRole['A'] ?? []);
            $denominador = array_sum($valuesByRole['DENOMINADOR'] ?? $valuesByRole['B'] ?? []);

            if ($numerador === 0.0 && empty($valuesByRole['NUMERADOR'] ?? $valuesByRole['A'] ?? [])) {
                return [
                    'status' => 'SIN_DATOS',
                    'value' => null,
                    'message' => 'No se pudo resolver el numerador de la formula.',
                    'used_values' => $usedValues,
                ];
            }

            if ($denominador === 0.0) {
                return [
                    'status' => 'ERROR_CALCULO',
                    'value' => null,
                    'message' => 'El denominador es cero o no pudo resolverse.',
                    'used_values' => $usedValues,
                ];
            }

            return [
                'status' => 'CALCULADO',
                'value' => ($numerador / $denominador) * $constante,
                'message' => null,
                'used_values' => $usedValues,
            ];
        }

        $allValues = collect($resolvedValues)->values();

        if ($allValues->count() === 1) {
            return [
                'status' => 'CALCULADO',
                'value' => ((float) $allValues->first()) * $constante,
                'message' => null,
                'used_values' => $usedValues,
            ];
        }

        if ($allValues->isNotEmpty()) {
            return [
                'status' => 'CALCULADO',
                'value' => ((float) $allValues->sum()) * $constante,
                'message' => 'Resultado obtenido por agregacion simple de variables disponibles.',
                'used_values' => $usedValues,
            ];
        }

        return [
            'status' => 'SIN_DATOS',
            'value' => null,
            'message' => 'No hay datos suficientes para calcular el indicador.',
            'used_values' => $usedValues,
        ];
    }

    private function estadoCorrida(string $codigo): EstadoCorrida
    {
        return EstadoCorrida::query()->where('codigo', $codigo)->firstOrFail();
    }

    private function estadoCorridaById(int $id): ?EstadoCorrida
    {
        return EstadoCorrida::find($id);
    }

    private function estadoResultado(string $codigo): EstadoResultado
    {
        return EstadoResultado::query()->where('codigo', $codigo)->firstOrFail();
    }
}
