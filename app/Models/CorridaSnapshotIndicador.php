<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorridaSnapshotIndicador extends Model
{
    public $timestamps = false;

    protected $table = 'corridas_snapshot_indicadores';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'valor_resultado' => 'decimal:6',
            'constante' => 'decimal:6',
            'publicable_en_corrida' => 'boolean',
            'periodo_referencia' => 'date',
            'created_at' => 'datetime',
        ];
    }
}
