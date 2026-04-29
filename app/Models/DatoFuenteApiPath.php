<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoFuenteApiPath extends Model
{
    protected $table = 'datos_fuente_api_paths';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'prioridad' => 'integer',
            'activo' => 'boolean',
        ];
    }
}
