<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatoFuenteApiConfig extends Model
{
    use SoftDeletes;

    protected $table = 'datos_fuente_api_configs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'headers_json' => 'array',
            'params_json' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function datoFuente()
    {
        return $this->belongsTo(DatoFuente::class);
    }

    public function paths()
    {
        return $this->hasMany(DatoFuenteApiPath::class)->orderBy('prioridad');
    }

    public function importaciones()
    {
        return $this->hasMany(DatoFuenteApiImportacion::class)->latest('fecha_importacion');
    }
}
