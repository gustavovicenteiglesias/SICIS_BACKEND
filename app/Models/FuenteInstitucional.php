<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuenteInstitucional extends Model
{
    use SoftDeletes;

    protected $table = 'fuentes_institucionales';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }
}
