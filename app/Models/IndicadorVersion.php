<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorVersion extends Model
{
    use SoftDeletes;

    protected $table = 'indicadores_versiones';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'constante' => 'decimal:6',
            'vigente_desde' => 'date',
            'vigente_hasta' => 'date',
            'activa' => 'boolean',
        ];
    }

    public function indicador()
    {
        return $this->belongsTo(Indicador::class);
    }

    public function tipoIndicador()
    {
        return $this->belongsTo(TipoIndicador::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function periodicidad()
    {
        return $this->belongsTo(Periodicidad::class);
    }

    public function variables()
    {
        return $this->hasMany(IndicadorVariable::class);
    }
}
