<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorridaSnapshotDato extends Model
{
    public $timestamps = false;

    protected $table = 'corridas_snapshot_datos';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'valor_dato' => 'decimal:6',
            'periodo_referencia' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function corrida()
    {
        return $this->belongsTo(Corrida::class);
    }

    public function datoFuente()
    {
        return $this->belongsTo(DatoFuente::class);
    }

    public function datoFuenteValor()
    {
        return $this->belongsTo(DatoFuenteValor::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
}
