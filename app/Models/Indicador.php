<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Indicador extends Model
{
    use SoftDeletes;

    protected $table = 'indicadores';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'publicable' => 'boolean',
            'sensible' => 'boolean',
            'activo' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function categoriaTematica()
    {
        return $this->belongsTo(CategoriaTematica::class);
    }

    public function normas()
    {
        return $this->belongsToMany(Norma::class, 'indicadores_normas', 'indicador_id', 'norma_id')
            ->withPivot(['codigo_en_norma', 'nombre_en_norma'])
            ->withTimestamps();
    }

    public function versiones()
    {
        return $this->hasMany(IndicadorVersion::class);
    }
}
