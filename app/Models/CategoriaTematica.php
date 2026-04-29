<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoriaTematica extends Model
{
    use SoftDeletes;

    protected $table = 'categorias_tematicas';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
            'orden' => 'integer',
        ];
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
