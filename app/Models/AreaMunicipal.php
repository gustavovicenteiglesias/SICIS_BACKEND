<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AreaMunicipal extends Model
{
    use SoftDeletes;

    protected $table = 'areas_municipales';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function datosFuente()
    {
        return $this->hasMany(DatoFuente::class);
    }
}
