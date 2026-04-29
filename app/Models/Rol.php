<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_roles', 'rol_id', 'usuario_id')
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos', 'rol_id', 'permiso_id')
            ->withTimestamps();
    }
}
