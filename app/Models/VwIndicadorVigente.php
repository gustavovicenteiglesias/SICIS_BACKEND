<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwIndicadorVigente extends Model
{
    public $timestamps = false;

    protected $table = 'vw_indicadores_vigentes';

    protected $primaryKey = 'indicador_version_id';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'constante' => 'decimal:6',
            'publicable' => 'boolean',
            'sensible' => 'boolean',
            'vigente_desde' => 'date',
            'vigente_hasta' => 'date',
        ];
    }
}
