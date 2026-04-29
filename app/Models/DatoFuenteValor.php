<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatoFuenteValor extends Model
{
    use SoftDeletes;

    protected $table = 'datos_fuente_valores';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'valor_crudo' => 'decimal:6',
            'valor_utilizado' => 'decimal:6',
            'periodo_referencia' => 'date',
            'fecha_produccion' => 'date',
            'fecha_carga' => 'datetime',
            'validado_at' => 'datetime',
            'vigente' => 'boolean',
        ];
    }
}
