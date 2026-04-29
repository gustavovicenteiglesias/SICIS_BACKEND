<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioRol extends Model
{
    use SoftDeletes;

    protected $table = 'usuarios_roles';

    protected $guarded = [];
}
