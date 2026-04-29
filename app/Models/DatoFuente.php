<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatoFuente extends Model
{
    use SoftDeletes;

    protected $table = 'datos_fuente';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'rango_minimo' => 'decimal:6',
            'rango_maximo' => 'decimal:6',
            'activo' => 'boolean',
        ];
    }

    public function valores()
    {
        return $this->hasMany(DatoFuenteValor::class);
    }
}
