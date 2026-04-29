<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoFuenteApiImportacion extends Model
{
    protected $table = 'datos_fuente_api_importaciones';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'fecha_importacion' => 'datetime',
            'valor_extraido' => 'decimal:6',
            'muestra_respuesta' => 'array',
        ];
    }

    public function apiConfig()
    {
        return $this->belongsTo(DatoFuenteApiConfig::class, 'dato_fuente_api_config_id');
    }
}
