<?php

namespace App\Support\Observability;

use App\Models\AlertaSistema;
use App\Models\AuditoriaLog;
use App\Models\NotificacionSistema;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Observability
{
    public static function audit(
        Request $request,
        string $tabla,
        ?int $registroId,
        string $accion,
        mixed $valorAnterior = null,
        mixed $valorNuevo = null,
        ?string $motivo = null
    ): AuditoriaLog {
        return AuditoriaLog::create([
            'usuario_id' => $request->user()?->id,
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'accion' => $accion,
            'valor_anterior' => self::normalizePayload($valorAnterior),
            'valor_nuevo' => self::normalizePayload($valorNuevo),
            'motivo' => $motivo,
            'ip_origen' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function alert(array $payload): AlertaSistema
    {
        return AlertaSistema::create([
            'tipo_alerta' => $payload['tipo_alerta'],
            'severidad' => $payload['severidad'],
            'titulo' => $payload['titulo'],
            'mensaje' => $payload['mensaje'],
            'entidad_tipo' => $payload['entidad_tipo'] ?? null,
            'entidad_id' => $payload['entidad_id'] ?? null,
            'estado' => $payload['estado'] ?? 'PENDIENTE',
            'usuario_asignado_id' => $payload['usuario_asignado_id'] ?? null,
            'resuelta_at' => $payload['resuelta_at'] ?? null,
        ]);
    }

    public static function alertWithInternalNotification(
        array $alertPayload,
        ?Usuario $recipient,
        ?string $subject = null,
        ?string $body = null
    ): AlertaSistema {
        $alertPayload['usuario_asignado_id'] = $alertPayload['usuario_asignado_id'] ?? $recipient?->id;

        $alert = self::alert($alertPayload);

        if ($recipient) {
            NotificacionSistema::create([
                'alerta_id' => $alert->id,
                'usuario_id' => $recipient->id,
                'canal' => 'INTERNA',
                'destinatario' => $recipient->email ?: $recipient->nombre_usuario,
                'asunto' => $subject ?: $alert->titulo,
                'cuerpo' => $body ?: $alert->mensaje,
                'estado' => 'PENDIENTE',
                'intentos' => 0,
            ]);
        }

        return $alert;
    }

    private static function normalizePayload(mixed $payload): mixed
    {
        if ($payload instanceof Model) {
            $payload = $payload->withoutRelations()->toArray();
        }

        if (!is_array($payload)) {
            return $payload;
        }

        return Arr::except($payload, [
            'password',
            'remember_token',
        ]);
    }
}
