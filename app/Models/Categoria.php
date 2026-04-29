<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use SoftDeletes;

    protected $table = 'categorias';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function categoriasTematicas()
    {
        return $this->hasMany(CategoriaTematica::class);
    }

    public function indicadores()
    {
        return $this->hasMany(Indicador::class);
    }
}
