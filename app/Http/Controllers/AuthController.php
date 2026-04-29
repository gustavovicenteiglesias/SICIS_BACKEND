<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('nombre_usuario', $request->usuario)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'usuario' => ['Las credenciales son incorrectas.'],
            ]);
        }

        if (!$usuario->activo) {
            throw ValidationException::withMessages([
                'usuario' => ['El usuario esta inactivo en el sistema.'],
            ]);
        }

        $token = $usuario->createToken('react-frontend')->plainTextToken;
        $usuario->update(['ultimo_acceso_at' => now()]);

        $rol = $usuario->roles()->first();

        return response()->json([
            'mensaje' => 'Login exitoso',
            'token' => $token,
            'usuario' => [
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'rol' => $rol?->codigo,
            ],
        ]);
    }
}
