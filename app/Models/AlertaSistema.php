<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaSistema extends Model
{
    protected $table = 'alertas_sistema';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'resuelta_at' => 'datetime',
        ];
    }

    public function usuarioAsignado()
    {
        return $this->belongsTo(Usuario::class, 'usuario_asignado_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(NotificacionSistema::class, 'alerta_id');
    }
}
