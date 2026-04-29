<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Norma extends Model
{
    use SoftDeletes;

    protected $table = 'normas';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
            'anio' => 'integer',
        ];
    }

    public function indicadores()
    {
        return $this->belongsToMany(Indicador::class, 'indicadores_normas', 'norma_id', 'indicador_id')
            ->withTimestamps();
    }
}
