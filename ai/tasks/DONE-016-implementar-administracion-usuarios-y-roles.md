# TAREA 016: Implementar Administracion De Usuarios Y Roles

## Estado

DONE

## Objetivo

Implementar los endpoints internos para administrar usuarios, roles y asignaciones, sobre el esquema actual del backend.

## Criterios De Aceptacion

- [x] CRUD basico de usuarios operativo.
- [x] CRUD basico de roles operativo.
- [x] Asignacion y remocion de roles a usuarios operativa.
- [x] Consulta de permisos efectivos por usuario disponible.
- [x] Proteccion por permisos aplicada.

## Archivos Involucrados

- `routes/api.php`
- `app/Http/Controllers/`
- `app/Models/Usuario.php`
- `app/Models/Rol.php`
- `app/Models/Permiso.php`
- `database/seeders/`

## Notas

- Mantener compatibilidad con el login actual.
- Evitar sobreingenieria en permisos; primero resolver ABM interno util.
- Depende del contrato definido en la tarea 015.
- Se implemento bajo `/api/seguridad`.
- La gestion de usuarios quedo protegida con `usuarios.gestionar`.
- La gestion de roles y permisos quedo protegida con `roles.gestionar`.
