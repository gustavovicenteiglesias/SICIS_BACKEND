<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionSistema extends Model
{
    protected $table = 'notificaciones_sistema';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'intentos' => 'integer',
            'enviada_at' => 'datetime',
        ];
    }

    public function alerta()
    {
        return $this->belongsTo(AlertaSistema::class, 'alerta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
