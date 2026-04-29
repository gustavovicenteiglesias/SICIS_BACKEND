<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'area_municipal_id',
        'nombre_usuario',
        'nombre',
        'apellido',
        'email',
        'password',
        'activo',
        'ultimo_acceso_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_acceso_at' => 'datetime',
        ];
    }

    public function areaMunicipal()
    {
        return $this->belongsTo(AreaMunicipal::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuarios_roles', 'usuario_id', 'rol_id')
            ->wherePivotNull('deleted_at')
            ->withTimestamps();
    }

    public function permisos(): Collection
    {
        return $this->roles
            ->loadMissing('permisos')
            ->flatMap(fn (Rol $rol) => $rol->permisos)
            ->unique('id')
            ->values();
    }

    public function hasPermission(string $codigo): bool
    {
        return $this->permisos()->contains('codigo', $codigo);
    }
}
