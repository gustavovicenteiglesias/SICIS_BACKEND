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

        $usuario = Usuario::with('roles')
            ->where('nombre_usuario', $request->usuario)
            ->first();

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

        $token = $usuario->createToken('sicis-api')->plainTextToken;
        $usuario->update(['ultimo_acceso_at' => now()]);

        return response()->json([
            'mensaje' => 'Login exitoso',
            'token' => $token,
            'usuario' => $this->usuarioResponse($usuario),
        ]);
    }

    public function perfil(Request $request)
    {
        $usuario = $request->user()->load('roles');

        return response()->json([
            'usuario' => $this->usuarioResponse($usuario),
        ]);
    }

    private function usuarioResponse(Usuario $usuario): array
    {
        return [
            'id' => $usuario->id,
            'nombre_usuario' => $usuario->nombre_usuario,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'email' => $usuario->email,
            'activo' => $usuario->activo,
            'roles' => $usuario->roles
                ->map(fn ($rol) => [
                    'id' => $rol->id,
                    'codigo' => $rol->codigo,
                    'nombre' => $rol->nombre,
                ])
                ->values(),
        ];
    }
}
