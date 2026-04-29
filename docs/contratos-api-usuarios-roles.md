# Contratos API - Usuarios Y Roles

Este documento define el contrato propuesto para la administracion interna de usuarios, roles y permisos del backend SICIS. No redefine el flujo de login existente; complementa la operacion administrativa del modulo de seguridad.

## Alcance

Este bloque cubre:

1. `usuarios`
2. `roles`
3. `permisos`
4. `usuarios_roles`
5. `roles_permisos`

Quedan fuera de este documento:

1. login y emision de tokens Sanctum
2. recuperacion de contrasena
3. politicas visuales o UX del panel de administracion

## Permisos Propuestos

- Administracion de usuarios: `usuarios.gestionar`
- Administracion de roles y permisos: `roles.gestionar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/seguridad`.
- Todas las respuestas son JSON.
- Los listados son paginados con `per_page`.
- Todas las operaciones requieren autenticacion Sanctum.
- `usuarios` usa soft delete.
- La asignacion de roles a usuarios se resuelve por `usuarios_roles`.
- Los permisos efectivos de un usuario se calculan por sus roles activos en `usuarios_roles` y los permisos asignados en `roles_permisos`.

## 1. Usuarios

Representa usuarios internos con acceso autenticado al backend.

### Rutas

```http
GET    /api/seguridad/usuarios
POST   /api/seguridad/usuarios
GET    /api/seguridad/usuarios/{usuario}
PUT    /api/seguridad/usuarios/{usuario}
DELETE /api/seguridad/usuarios/{usuario}
```

### Request `POST/PUT`

```json
{
  "area_municipal_id": 1,
  "nombre_usuario": "mlopez",
  "nombre": "Maria",
  "apellido": "Lopez",
  "email": "mlopez@lujan.gob.ar",
  "password": "12345678",
  "activo": true
}
```

### Response `show`

```json
{
  "id": 2,
  "area_municipal_id": 1,
  "nombre_usuario": "mlopez",
  "nombre": "Maria",
  "apellido": "Lopez",
  "email": "mlopez@lujan.gob.ar",
  "activo": true,
  "ultimo_acceso_at": "2026-04-29T12:00:00.000000Z",
  "roles": [],
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

## 2. Roles

Representa perfiles funcionales o administrativos agrupados por permisos.

### Rutas

```http
GET    /api/seguridad/roles
POST   /api/seguridad/roles
GET    /api/seguridad/roles/{rol}
PUT    /api/seguridad/roles/{rol}
DELETE /api/seguridad/roles/{rol}
```

### Request `POST/PUT`

```json
{
  "codigo": "RESPONSABLE_SALUD",
  "nombre": "Responsable Salud",
  "descripcion": "Gestion operativa del area de salud",
  "activo": true
}
```

### Response `show`

```json
{
  "id": 7,
  "codigo": "RESPONSABLE_SALUD",
  "nombre": "Responsable Salud",
  "descripcion": "Gestion operativa del area de salud",
  "activo": true,
  "permisos": [],
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

## 3. Asignacion De Roles A Usuarios

Resuelve la vinculacion many-to-many entre usuarios y roles.

### Rutas

```http
GET    /api/seguridad/usuarios/{usuario}/roles
POST   /api/seguridad/usuarios/{usuario}/roles
DELETE /api/seguridad/usuarios/{usuario}/roles/{rol}
```

### Request `POST`

```json
{
  "rol_id": 7
}
```

### Response `GET`

```json
{
  "data": [
    {
      "id": 7,
      "codigo": "RESPONSABLE_SALUD",
      "nombre": "Responsable Salud"
    }
  ]
}
```

## 4. Asignacion De Permisos A Roles

Resuelve la vinculacion many-to-many entre roles y permisos.

### Rutas

```http
GET    /api/seguridad/roles/{rol}/permisos
POST   /api/seguridad/roles/{rol}/permisos
DELETE /api/seguridad/roles/{rol}/permisos/{permiso}
```

### Request `POST`

```json
{
  "permiso_id": 11
}
```

## 5. Permisos Efectivos De Usuario

Permite inspeccionar el conjunto real de permisos resultante de todos los roles asignados a un usuario.

### Rutas

```http
GET /api/seguridad/usuarios/{usuario}/permisos-efectivos
```

### Response

```json
{
  "usuario_id": 2,
  "permisos": [
    {
      "id": 11,
      "codigo": "datos_fuente.ver",
      "nombre": "Ver datos fuente"
    },
    {
      "id": 13,
      "codigo": "datos_fuente.cargar",
      "nombre": "Cargar datos fuente"
    }
  ]
}
```

## Filtros Propuestos

### Usuarios

```http
GET /api/seguridad/usuarios?per_page=15&activo=true&area_municipal_id=1
```

Filtros:

- `per_page`
- `activo`
- `area_municipal_id`
- `nombre_usuario`
- `email`
- `nombre`
- `apellido`

### Roles

```http
GET /api/seguridad/roles?per_page=15&activo=true
```

Filtros:

- `per_page`
- `activo`
- `codigo`
- `nombre`

## Reglas De Negocio A Respetar

1. `usuarios.nombre_usuario` debe ser unico.
2. `usuarios.email` debe ser unico.
3. `roles.codigo` debe ser unico.
4. `roles.nombre` debe ser unico.
5. No se debe devolver `password` en responses.
6. La baja de usuario debe ser logica, no fisica.
7. La dupla `usuario_id + rol_id` debe mantenerse unica.
8. La dupla `rol_id + permiso_id` debe mantenerse unica.
9. El login sigue usando `nombre_usuario` y no se redisenia en este bloque.
10. Los permisos efectivos se calculan por roles, no por asignacion directa a usuario.

## Decisiones Operativas Para La Implementacion

1. El modulo se expone bajo `/api/seguridad`.
2. La administracion de usuarios requiere `usuarios.gestionar`.
3. La administracion de roles y permisos requiere `roles.gestionar`.
4. La asignacion de roles a usuarios se resuelve con endpoints propios y no embebida obligatoriamente en el payload del usuario.
5. La consulta de permisos efectivos se expone como endpoint de solo lectura para diagnostico operativo.
