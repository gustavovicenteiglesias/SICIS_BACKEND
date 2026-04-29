<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE VIEW vw_indicadores_vigentes AS
            SELECT
              i.id AS indicador_id,
              i.codigo_interno,
              i.nombre AS indicador,
              c.nombre AS categoria,
              ct.nombre AS categoria_tematica,
              ti.codigo AS tipo_indicador,
              iv.id AS indicador_version_id,
              iv.version,
              iv.formula_tipo,
              iv.constante,
              iv.formula_texto,
              iv.formula_expression,
              um.nombre AS unidad_medida,
              um.simbolo AS unidad_simbolo,
              p.nombre AS periodicidad,
              i.publicable,
              i.sensible,
              iv.vigente_desde,
              iv.vigente_hasta
            FROM indicadores i
            JOIN categorias c ON c.id = i.categoria_id
            LEFT JOIN categorias_tematicas ct ON ct.id = i.categoria_tematica_id
            JOIN indicadores_versiones iv ON iv.indicador_id = i.id
            JOIN tipos_indicador ti ON ti.id = iv.tipo_indicador_id
            JOIN unidades_medida um ON um.id = iv.unidad_medida_id
            JOIN periodicidades p ON p.id = iv.periodicidad_id
            WHERE i.deleted_at IS NULL
              AND i.activo = 1
              AND iv.deleted_at IS NULL
              AND iv.activa = 1
              AND (iv.vigente_hasta IS NULL OR iv.vigente_hasta >= CURRENT_DATE)
        SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW vw_resultados_publicos AS
            SELECT
              c.id AS corrida_id,
              j.nombre AS jurisdiccion,
              c.periodo_referencia,
              i.codigo_interno,
              i.nombre AS indicador,
              cat.nombre AS categoria,
              um.nombre AS unidad_medida,
              um.simbolo AS unidad_simbolo,
              r.valor_resultado,
              r.created_at AS calculado_at,
              c.publicada_at
            FROM corridas_snapshot_indicadores r
            JOIN corridas c ON c.id = r.corrida_id
            JOIN estados_corrida ec ON ec.id = c.estado_corrida_id
            JOIN indicadores i ON i.id = r.indicador_id
            JOIN categorias cat ON cat.id = i.categoria_id
            JOIN unidades_medida um ON um.id = r.unidad_medida_id
            JOIN jurisdicciones j ON j.id = c.jurisdiccion_id
            WHERE ec.codigo = 'PUBLICADA'
              AND c.publicada_at IS NOT NULL
              AND i.publicable = 1
              AND r.publicable_en_corrida = 1
              AND i.sensible = 0
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_resultados_publicos');
        DB::statement('DROP VIEW IF EXISTS vw_indicadores_vigentes');
    }
};
