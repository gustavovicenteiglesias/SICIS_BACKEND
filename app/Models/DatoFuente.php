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

    public function areaMunicipal()
    {
        return $this->belongsTo(AreaMunicipal::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function periodicidad()
    {
        return $this->belongsTo(Periodicidad::class);
    }

    public function modalidadCarga()
    {
        return $this->belongsTo(ModalidadCarga::class);
    }

    public function fuenteInstitucional()
    {
        return $this->belongsTo(FuenteInstitucional::class);
    }

    public function responsableUsuario()
    {
        return $this->belongsTo(Usuario::class, 'responsable_usuario_id');
    }

    public function apiConfigs()
    {
        return $this->hasMany(DatoFuenteApiConfig::class);
    }
}
