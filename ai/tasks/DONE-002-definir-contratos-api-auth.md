# TAREA 002: Definir Contratos API De Auth

## Estado

DONE

## Objetivo

Documentar y estabilizar los endpoints iniciales de autenticacion y perfil para que el frontend externo pueda consumirlos.

## Criterios De Aceptacion

- [x] Contrato de `POST /api/login` documentado.
- [x] Contrato de `GET /api/perfil` documentado.
- [x] Respuesta de usuario incluye roles de forma consistente.
- [x] Errores de credenciales e inactividad son claros.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/AuthController.php`
- `README.md`

## Notas

- El body puede seguir usando `usuario`, aunque el campo interno sea `nombre_usuario`.
- No crear vistas ni componentes.
- Login y perfil devuelven el mismo shape de usuario con `roles`.
