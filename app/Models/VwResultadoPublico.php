<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwResultadoPublico extends Model
{
    public $timestamps = false;

    protected $table = 'vw_resultados_publicos';

    protected $primaryKey = 'corrida_id';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'periodo_referencia' => 'date',
            'valor_resultado' => 'decimal:6',
            'calculado_at' => 'datetime',
            'publicada_at' => 'datetime',
        ];
    }
}
