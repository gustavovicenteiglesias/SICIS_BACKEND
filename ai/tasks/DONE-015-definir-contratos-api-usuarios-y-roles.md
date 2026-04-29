# TAREA 015: Definir Contratos API De Usuarios Y Roles

## Estado

DONE

## Objetivo

Documentar el contrato API para administracion interna de usuarios, roles y permisos, sin alterar la estructura de autenticacion ya establecida.

## Criterios De Aceptacion

- [x] Documentar endpoints para usuarios.
- [x] Documentar endpoints para roles.
- [x] Documentar asignacion de roles a usuarios.
- [x] Documentar consultas de permisos efectivos.
- [x] Definir permisos requeridos para administracion de seguridad.

## Archivos Involucrados

- `docs/`
- `app/Models/Usuario.php`
- `app/Models/Rol.php`
- `app/Models/Permiso.php`

## Notas

- Respetar la estructura actual con `usuarios_roles`.
- No redisenar auth ni login.
- Mantener separado lo operativo de seguridad de lo funcional del dominio.
- Se propuso `/api/seguridad` como prefijo del modulo.
- Se separo administracion de usuarios de administracion de roles y permisos.
