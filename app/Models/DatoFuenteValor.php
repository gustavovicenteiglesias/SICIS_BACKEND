<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DatoFuenteValor extends Model
{
    use SoftDeletes;

    protected $table = 'datos_fuente_valores';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'valor_crudo' => 'decimal:6',
            'valor_utilizado' => 'decimal:6',
            'periodo_referencia' => 'date',
            'fecha_produccion' => 'date',
            'fecha_carga' => 'datetime',
            'validado_at' => 'datetime',
            'vigente' => 'boolean',
        ];
    }

    public function datoFuente()
    {
        return $this->belongsTo(DatoFuente::class);
    }

    public function jurisdiccion()
    {
        return $this->belongsTo(Jurisdiccion::class);
    }

    public function estadoDato()
    {
        return $this->belongsTo(EstadoDato::class);
    }

    public function modalidadCarga()
    {
        return $this->belongsTo(ModalidadCarga::class);
    }

    public function usuarioCarga()
    {
        return $this->belongsTo(Usuario::class, 'usuario_carga_id');
    }

    public function usuarioValida()
    {
        return $this->belongsTo(Usuario::class, 'usuario_valida_id');
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciaDato::class, 'dato_fuente_valor_id');
    }
}
