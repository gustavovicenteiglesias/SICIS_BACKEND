<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvidenciaDato extends Model
{
    protected $table = 'evidencias_dato';

    protected $guarded = [];

    public function datoFuenteValor()
    {
        return $this->belongsTo(DatoFuenteValor::class, 'dato_fuente_valor_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
