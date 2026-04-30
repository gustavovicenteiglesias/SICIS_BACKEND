<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corrida extends Model
{
    protected $table = 'corridas';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'periodo_referencia' => 'date',
            'ejecutada_at' => 'datetime',
            'aprobada_at' => 'datetime',
            'publicada_at' => 'datetime',
        ];
    }

    public function jurisdiccion()
    {
        return $this->belongsTo(Jurisdiccion::class);
    }

    public function estadoCorrida()
    {
        return $this->belongsTo(EstadoCorrida::class);
    }

    public function usuarioEjecucion()
    {
        return $this->belongsTo(Usuario::class, 'usuario_ejecucion_id');
    }

    public function usuarioAprobacion()
    {
        return $this->belongsTo(Usuario::class, 'usuario_aprobacion_id');
    }

    public function snapshotDatos()
    {
        return $this->hasMany(CorridaSnapshotDato::class);
    }

    public function snapshotIndicadores()
    {
        return $this->hasMany(CorridaSnapshotIndicador::class);
    }
}
