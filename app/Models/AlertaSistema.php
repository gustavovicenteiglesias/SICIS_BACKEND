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
}
