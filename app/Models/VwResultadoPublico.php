<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwResultadoPublico extends Model
{
    public $timestamps = false;

    protected $table = 'vw_resultados_publicos';

    protected $primaryKey = 'corrida_id';

    protected $guarded = [];
}
