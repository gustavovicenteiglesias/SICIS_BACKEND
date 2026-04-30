<?php

namespace App\Http\Controllers\DatosFuente;

use App\Http\Controllers\Controller;
use App\Models\DatoFuente;
use App\Models\DatoFuenteApiConfig;
use App\Models\DatoFuenteApiImportacion;
use App\Models\DatoFuenteValor;
use App\Models\EstadoDato;
use App\Models\Jurisdiccion;
use App\Models\ModalidadCarga;
use Carbon\Carbon;
use RuntimeException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class DatoFuenteApiConfigController extends Controller
{
    public function index(Request $request, string $datoFuenteId): JsonResponse
    {
        $datoFuente = DatoFuente::findOrFail($datoFuenteId);
        $query = $datoFuente->apiConfigs()->with(['paths']);

        foreach (['metodo_http', 'auth_tipo'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('activo')) {
            $query->where('activo', filter_var($request->input('activo'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->orderBy('nombre')->paginate($perPage));
    }

    public function store(Request $request, string $datoFuenteId): JsonResponse
    {
        $datoFuente = DatoFuente::findOrFail($datoFuenteId);
        $data = $request->validate($this->storeRules());

        $config = $datoFuente->apiConfigs()->create($this->payload($data));

        return response()->json($this->loadConfig($config), 201);
    }

    public function show(string $datoFuenteId, string $id): JsonResponse
    {
        return response()->json($this->findConfig($datoFuenteId, $id));
    }

    public function update(Request $request, string $datoFuenteId, string $id): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $id);
        $data = $request->validate($this->updateRules());

        $config->update($this->payload($data));

        return response()->json($this->loadConfig($config));
    }

    public function destroy(string $datoFuenteId, string $id): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $id);
        $config->delete();

        return response()->noContent();
    }

    public function probar(Request $request, string $datoFuenteId, string $id): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $id);
        $execution = $this->executeConfig($config);

        $importacion = $this->recordImportacion($config, [
            'estado' => $execution['ok'] ? 'PRUEBA_OK' : 'PRUEBA_ERROR',
            'http_status' => $execution['http_status'],
            'json_path_usado' => $execution['json_path_usado'],
            'valor_extraido' => $execution['valor_extraido'],
            'mensaje_error' => $execution['error'],
            'muestra_respuesta' => $execution['muestra_respuesta'],
        ]);

        $status = $execution['ok'] ? 200 : 422;

        return response()->json([
            'ok' => $execution['ok'],
            'config' => $this->loadConfig($config),
            'resultado' => [
                'http_status' => $execution['http_status'],
                'json_path_usado' => $execution['json_path_usado'],
                'valor_extraido' => $execution['valor_extraido'],
                'periodo_extraido' => $execution['periodo_extraido'],
                'jurisdiccion_extraida' => $execution['jurisdiccion_extraida'],
                'mensaje_error' => $execution['error'],
            ],
            'importacion' => $importacion,
        ], $status);
    }

    public function importar(Request $request, string $datoFuenteId, string $id): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $id);
        $data = $request->validate([
            'jurisdiccion_id' => 'nullable|integer|exists:jurisdicciones,id',
            'periodo_referencia' => 'nullable|date',
            'fecha_produccion' => 'nullable|date',
            'estado_dato_id' => 'nullable|integer|exists:estados_dato,id',
            'vigente' => 'nullable|boolean',
        ]);

        $execution = $this->executeConfig($config);

        if (!$execution['ok']) {
            $importacion = $this->recordImportacion($config, [
                'estado' => 'ERROR',
                'http_status' => $execution['http_status'],
                'json_path_usado' => $execution['json_path_usado'],
                'valor_extraido' => $execution['valor_extraido'],
                'mensaje_error' => $execution['error'],
                'muestra_respuesta' => $execution['muestra_respuesta'],
            ]);

            return response()->json([
                'ok' => false,
                'mensaje' => 'No se pudo importar el valor desde la API configurada.',
                'importacion' => $importacion,
            ], 422);
        }

        try {
            $jurisdiccion = $this->resolveJurisdiccion($execution['jurisdiccion_extraida'], $data['jurisdiccion_id'] ?? null);
            $periodo = $this->resolvePeriodo($execution['periodo_extraido'], $data['periodo_referencia'] ?? null);

            if (!$jurisdiccion) {
                throw ValidationException::withMessages([
                    'jurisdiccion_id' => ['No se pudo resolver la jurisdiccion de la importacion.'],
                ]);
            }

            if (!$periodo) {
                throw ValidationException::withMessages([
                    'periodo_referencia' => ['No se pudo resolver el periodo de referencia de la importacion.'],
                ]);
            }

            $datoFuente = $config->datoFuente;
            $this->ensureRange($datoFuente, $execution['valor_extraido'], 'valor_extraido');

            $estadoDato = isset($data['estado_dato_id'])
                ? EstadoDato::findOrFail($data['estado_dato_id'])
                : EstadoDato::query()->where('codigo', 'CARGADO')->firstOrFail();

            $modalidadApi = ModalidadCarga::query()->where('codigo', 'API')->firstOrFail();

            $valor = DatoFuenteValor::query()
                ->where('dato_fuente_id', $datoFuente->id)
                ->where('jurisdiccion_id', $jurisdiccion->id)
                ->whereDate('periodo_referencia', $periodo->toDateString())
                ->latest('id')
                ->first();

            $payload = [
                'dato_fuente_id' => $datoFuente->id,
                'jurisdiccion_id' => $jurisdiccion->id,
                'estado_dato_id' => $estadoDato->id,
                'modalidad_carga_id' => $modalidadApi->id,
                'usuario_carga_id' => $request->user()->id,
                'valor_crudo' => $execution['valor_extraido'],
                'valor_utilizado' => null,
                'periodo_referencia' => $periodo->toDateString(),
                'fecha_produccion' => isset($data['fecha_produccion']) ? Carbon::parse($data['fecha_produccion'])->toDateString() : null,
                'fecha_carga' => now(),
                'observado_motivo' => null,
                'vigente' => $data['vigente'] ?? true,
            ];

            if ($valor) {
                $valor->update($payload);
            } else {
                $valor = DatoFuenteValor::create($payload);
            }

            $importacion = $this->recordImportacion($config, [
                'estado' => 'OK',
                'http_status' => $execution['http_status'],
                'json_path_usado' => $execution['json_path_usado'],
                'valor_extraido' => $execution['valor_extraido'],
                'mensaje_error' => null,
                'muestra_respuesta' => $execution['muestra_respuesta'],
            ]);

            return response()->json([
                'ok' => true,
                'valor' => $valor->fresh([
                    'datoFuente',
                    'jurisdiccion',
                    'estadoDato',
                    'modalidadCarga',
                    'usuarioCarga',
                    'usuarioValida',
                    'evidencias',
                ]),
                'importacion' => $importacion,
            ]);
        } catch (\Throwable $e) {
            $importacion = $this->recordImportacion($config, [
                'estado' => 'ERROR',
                'http_status' => $execution['http_status'],
                'json_path_usado' => $execution['json_path_usado'],
                'valor_extraido' => $execution['valor_extraido'],
                'mensaje_error' => $e->getMessage(),
                'muestra_respuesta' => $execution['muestra_respuesta'],
            ]);

            if ($e instanceof ValidationException) {
                throw ValidationException::withMessages([
                    ...$e->errors(),
                    'importacion_id' => [$importacion->id],
                ]);
            }

            throw new RuntimeException(
                'Ocurrio un error al persistir la importacion. Importacion registrada: '.$importacion->id,
                previous: $e
            );
        }
    }

    public function importaciones(Request $request, string $datoFuenteId, string $id): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $id);
        $query = $config->importaciones();

        foreach (['estado', 'http_status'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_importacion', '>=', Carbon::parse($request->input('fecha_desde'))->startOfDay());
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_importacion', '<=', Carbon::parse($request->input('fecha_hasta'))->endOfDay());
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->paginate($perPage));
    }

    public function showImportacion(string $datoFuenteId, string $id, string $importacionId): JsonResponse
    {
        $config = $this->findConfig($datoFuenteId, $id);

        return response()->json(
            $config->importaciones()->findOrFail($importacionId)
        );
    }

    private function storeRules(): array
    {
        return [
            'nombre' => 'required|string|max:180',
            'metodo_http' => 'required|string|max:10|in:GET,POST,PUT,PATCH,DELETE',
            'url' => 'required|string|max:700',
            'auth_tipo' => 'nullable|string|max:50',
            'headers_json' => 'nullable|array',
            'params_json' => 'nullable|array',
            'json_path_valor' => 'required|string|max:255',
            'json_path_periodo' => 'nullable|string|max:255',
            'json_path_jurisdiccion' => 'nullable|string|max:255',
            'unidad_esperada' => 'nullable|string|max:80',
            'activo' => 'nullable|boolean',
        ];
    }

    private function updateRules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:180',
            'metodo_http' => 'sometimes|required|string|max:10|in:GET,POST,PUT,PATCH,DELETE',
            'url' => 'sometimes|required|string|max:700',
            'auth_tipo' => 'nullable|string|max:50',
            'headers_json' => 'nullable|array',
            'params_json' => 'nullable|array',
            'json_path_valor' => 'sometimes|required|string|max:255',
            'json_path_periodo' => 'nullable|string|max:255',
            'json_path_jurisdiccion' => 'nullable|string|max:255',
            'unidad_esperada' => 'nullable|string|max:80',
            'activo' => 'nullable|boolean',
        ];
    }

    private function payload(array $data): array
    {
        return collect($data)->only([
            'nombre',
            'metodo_http',
            'url',
            'auth_tipo',
            'headers_json',
            'params_json',
            'json_path_valor',
            'json_path_periodo',
            'json_path_jurisdiccion',
            'unidad_esperada',
            'activo',
        ])->all();
    }

    private function findConfig(string $datoFuenteId, string $id): DatoFuenteApiConfig
    {
        return DatoFuenteApiConfig::query()
            ->where('dato_fuente_id', $datoFuenteId)
            ->with(['paths'])
            ->findOrFail($id);
    }

    private function loadConfig(DatoFuenteApiConfig $config): DatoFuenteApiConfig
    {
        return $config->fresh(['paths']);
    }

    private function executeConfig(DatoFuenteApiConfig $config): array
    {
        $response = null;
        $body = null;

        try {
            $pending = Http::timeout(30)
                ->acceptJson()
                ->withHeaders($config->headers_json ?? []);

            $method = strtoupper($config->metodo_http);

            if ($method === 'GET') {
                $response = $pending->get($config->url, $config->params_json ?? []);
            } else {
                $response = $pending->send($method, $config->url, [
                    'query' => $config->params_json ?? [],
                ]);
            }

            $body = $response->json();

            if (!is_array($body)) {
                return [
                    'ok' => false,
                    'http_status' => $response->status(),
                    'json_path_usado' => null,
                    'valor_extraido' => null,
                    'periodo_extraido' => null,
                    'jurisdiccion_extraida' => null,
                    'muestra_respuesta' => $this->responseSample($response, null),
                    'error' => 'La respuesta no pudo interpretarse como JSON asociativo.',
                ];
            }

            $resolved = $this->resolveValuePath($config, $body);

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'http_status' => $response->status(),
                    'json_path_usado' => $resolved['path'],
                    'valor_extraido' => $resolved['value'],
                    'periodo_extraido' => data_get($body, $config->json_path_periodo),
                    'jurisdiccion_extraida' => data_get($body, $config->json_path_jurisdiccion),
                    'muestra_respuesta' => $this->responseSample($response, $body),
                    'error' => 'La API respondio con un estado HTTP no exitoso.',
                ];
            }

            if ($resolved['value'] === null || !is_numeric($resolved['value'])) {
                return [
                    'ok' => false,
                    'http_status' => $response->status(),
                    'json_path_usado' => $resolved['path'],
                    'valor_extraido' => $resolved['value'],
                    'periodo_extraido' => data_get($body, $config->json_path_periodo),
                    'jurisdiccion_extraida' => data_get($body, $config->json_path_jurisdiccion),
                    'muestra_respuesta' => $this->responseSample($response, $body),
                    'error' => 'No se pudo extraer un valor numerico usando los paths configurados.',
                ];
            }

            return [
                'ok' => true,
                'http_status' => $response->status(),
                'json_path_usado' => $resolved['path'],
                'valor_extraido' => (float) $resolved['value'],
                'periodo_extraido' => $config->json_path_periodo ? data_get($body, $config->json_path_periodo) : null,
                'jurisdiccion_extraida' => $config->json_path_jurisdiccion ? data_get($body, $config->json_path_jurisdiccion) : null,
                'muestra_respuesta' => $this->responseSample($response, $body),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'http_status' => $response?->status(),
                'json_path_usado' => null,
                'valor_extraido' => null,
                'periodo_extraido' => null,
                'jurisdiccion_extraida' => null,
                'muestra_respuesta' => $this->responseSample($response, $body),
                'error' => $e->getMessage(),
            ];
        }
    }

    private function resolveValuePath(DatoFuenteApiConfig $config, array $body): array
    {
        $paths = collect([$config->json_path_valor])
            ->merge(
                $config->paths
                    ->where('activo', true)
                    ->sortBy('prioridad')
                    ->pluck('json_path_valor')
            )
            ->filter()
            ->unique()
            ->values();

        foreach ($paths as $path) {
            $value = data_get($body, $path);

            if ($value !== null) {
                return [
                    'path' => $path,
                    'value' => $value,
                ];
            }
        }

        return [
            'path' => $paths->first(),
            'value' => null,
        ];
    }

    private function responseSample(?Response $response, mixed $body): mixed
    {
        if (is_array($body)) {
            return $body;
        }

        if ($response) {
            return ['raw' => mb_substr($response->body(), 0, 2000)];
        }

        return null;
    }

    private function recordImportacion(DatoFuenteApiConfig $config, array $payload): DatoFuenteApiImportacion
    {
        return $config->importaciones()->create([
            'fecha_importacion' => now(),
            'estado' => $payload['estado'],
            'http_status' => $payload['http_status'],
            'json_path_usado' => $payload['json_path_usado'],
            'valor_extraido' => $payload['valor_extraido'],
            'mensaje_error' => $payload['mensaje_error'],
            'muestra_respuesta' => $payload['muestra_respuesta'],
        ]);
    }

    private function resolveJurisdiccion(mixed $extracted, ?int $fallbackId): ?Jurisdiccion
    {
        if ($fallbackId) {
            return Jurisdiccion::find($fallbackId);
        }

        if ($extracted === null || $extracted === '') {
            return null;
        }

        if (is_numeric($extracted)) {
            return Jurisdiccion::find((int) $extracted);
        }

        return Jurisdiccion::query()
            ->where('nombre', $extracted)
            ->orWhere('codigo_oficial', $extracted)
            ->first();
    }

    private function resolvePeriodo(mixed $extracted, mixed $fallback): ?Carbon
    {
        $source = $fallback ?: $extracted;

        if ($source === null || $source === '') {
            return null;
        }

        try {
            return Carbon::parse($source);
        } catch (\Throwable) {
            return null;
        }
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
}
