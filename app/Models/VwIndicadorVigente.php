<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VwIndicadorVigente extends Model
{
    public $timestamps = false;

    protected $table = 'vw_indicadores_vigentes';

    protected $primaryKey = 'indicador_version_id';

    protected $guarded = [];
}
