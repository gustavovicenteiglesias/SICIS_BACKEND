<?php

namespace App\Http\Controllers\Externo;

use App\Http\Controllers\Controller;
use App\Models\Corrida;
use App\Models\VwIndicadorVigente;
use App\Models\VwResultadoPublico;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConsultaExternaController extends Controller
{
    public function indicadoresVigentes(Request $request): JsonResponse|StreamedResponse
    {
        $query = VwIndicadorVigente::query();

        foreach (['codigo_interno', 'categoria', 'categoria_tematica', 'tipo_indicador', 'periodicidad'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'like', '%'.$request->input($filter).'%');
            }
        }

        if ($request->filled('publicable')) {
            $query->where('publicable', filter_var($request->input('publicable'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('sensible')) {
            $query->where('sensible', filter_var($request->input('sensible'), FILTER_VALIDATE_BOOLEAN));
        }

        $query->orderBy('categoria')->orderBy('indicador');

        return $this->respond($request, $query, [
            'indicador_id',
            'codigo_interno',
            'indicador',
            'categoria',
            'categoria_tematica',
            'tipo_indicador',
            'indicador_version_id',
            'version',
            'formula_tipo',
            'constante',
            'formula_texto',
            'formula_expression',
            'unidad_medida',
            'unidad_simbolo',
            'periodicidad',
            'publicable',
            'sensible',
            'vigente_desde',
            'vigente_hasta',
        ], 'indicadores_vigentes');
    }

    public function resultadosPublicos(Request $request): JsonResponse|StreamedResponse
    {
        $query = VwResultadoPublico::query();

        foreach (['jurisdiccion', 'codigo_interno', 'indicador', 'categoria'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, 'like', '%'.$request->input($filter).'%');
            }
        }

        $this->applyDateRange($request, $query, 'periodo_referencia');
        $this->applyDateRange($request, $query, 'publicada_at', 'publicada_desde', 'publicada_hasta');

        $query->orderByDesc('periodo_referencia')->orderBy('jurisdiccion')->orderBy('indicador');

        return $this->respond($request, $query, [
            'corrida_id',
            'jurisdiccion',
            'periodo_referencia',
            'codigo_interno',
            'indicador',
            'categoria',
            'unidad_medida',
            'unidad_simbolo',
            'valor_resultado',
            'calculado_at',
            'publicada_at',
        ], 'resultados_publicos');
    }

    public function corridasPublicadas(Request $request): JsonResponse|StreamedResponse
    {
        $query = Corrida::query()
            ->select([
                'corridas.id',
                'corridas.titulo',
                'corridas.jurisdiccion_id',
                'corridas.periodo_referencia',
                'corridas.ejecutada_at',
                'corridas.aprobada_at',
                'corridas.publicada_at',
            ])
            ->with(['jurisdiccion'])
            ->whereHas('estadoCorrida', fn (Builder $builder) => $builder->where('codigo', 'PUBLICADA'))
            ->whereNotNull('publicada_at')
            ->withCount([
                'snapshotIndicadores as resultados_publicables_count' => fn (Builder $builder) => $builder->where('publicable_en_corrida', true),
            ]);

        if ($request->filled('jurisdiccion_id')) {
            $query->where('jurisdiccion_id', $request->input('jurisdiccion_id'));
        }

        if ($request->filled('titulo')) {
            $query->where('titulo', 'like', '%'.$request->input('titulo').'%');
        }

        $this->applyDateRange($request, $query, 'periodo_referencia');
        $this->applyDateRange($request, $query, 'publicada_at', 'publicada_desde', 'publicada_hasta');

        $query->orderByDesc('publicada_at')->orderByDesc('id');

        if ($this->wantsCsv($request)) {
            $rows = $query->get()->map(fn (Corrida $corrida) => [
                'id' => $corrida->id,
                'titulo' => $corrida->titulo,
                'jurisdiccion' => $corrida->jurisdiccion?->nombre,
                'periodo_referencia' => optional($corrida->periodo_referencia)->toDateString(),
                'ejecutada_at' => optional($corrida->ejecutada_at)?->toISOString(),
                'aprobada_at' => optional($corrida->aprobada_at)?->toISOString(),
                'publicada_at' => optional($corrida->publicada_at)?->toISOString(),
                'resultados_publicables_count' => $corrida->resultados_publicables_count,
            ])->all();

            return $this->csvResponse('corridas_publicadas', $rows);
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->paginate($perPage));
    }

    private function respond(Request $request, Builder $query, array $columns, string $filename): JsonResponse|StreamedResponse
    {
        if ($this->wantsCsv($request)) {
            return $this->csvResponse($filename, $query->get($columns)->map->toArray()->all());
        }

        $perPage = max(1, min((int) $request->integer('per_page', 15), 100));

        return response()->json($query->paginate($perPage, $columns));
    }

    private function wantsCsv(Request $request): bool
    {
        return strtolower((string) $request->input('format')) === 'csv';
    }

    private function csvResponse(string $filename, array $rows): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'_'.now()->format('Ymd_His').'.csv"',
        ];

        return response()->streamDownload(function () use ($rows): void {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            if (empty($rows)) {
                fputcsv($output, ['sin_datos']);
                fclose($output);

                return;
            }

            fputcsv($output, array_keys($rows[0]));

            foreach ($rows as $row) {
                fputcsv($output, $row);
            }

            fclose($output);
        }, $filename.'.csv', $headers);
    }

    private function applyDateRange(
        Request $request,
        Builder $query,
        string $column,
        string $fromKey = 'periodo_desde',
        string $toKey = 'periodo_hasta'
    ): void {
        if ($request->filled($fromKey)) {
            $query->where($column, '>=', Carbon::parse($request->input($fromKey))->startOfDay());
        }

        if ($request->filled($toKey)) {
            $query->where($column, '<=', Carbon::parse($request->input($toKey))->endOfDay());
        }
    }
}
