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
}
