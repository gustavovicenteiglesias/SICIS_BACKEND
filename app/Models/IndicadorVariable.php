<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndicadorVariable extends Model
{
    use SoftDeletes;

    protected $table = 'indicadores_variables';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'obligatorio' => 'boolean',
            'orden' => 'integer',
        ];
    }
}
