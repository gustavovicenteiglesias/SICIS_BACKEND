<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurisdiccion extends Model
{
    use SoftDeletes;

    protected $table = 'jurisdicciones';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function tipoJurisdiccion()
    {
        return $this->belongsTo(TipoJurisdiccion::class);
    }

    public function jurisdiccionPadre()
    {
        return $this->belongsTo(self::class, 'jurisdiccion_padre_id');
    }

    public function jurisdiccionesHijas()
    {
        return $this->hasMany(self::class, 'jurisdiccion_padre_id');
    }
}
