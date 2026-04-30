# Contratos API \- Usuarios Y Roles

Este documento define el contrato propuesto para la administración interna de usuarios, roles y permisos del backend SICIS. No redefine el flujo de login existente; complementa la operación administrativa del módulo de seguridad.

## Alcance

Este bloque cubre:

1. `usuarios`  
2. `roles`  
3. `permisos`  
4. `usuarios_roles`  
5. `roles_permisos`

Quedan fuera de este documento:

1. login y emisión de tokens Sanctum  
2. recuperación de contraseña  
3. políticas visuales o UX del panel de administración

## Permisos Propuestos

- Administración de usuarios: `usuarios.gestionar`  
- Administración de roles y permisos: `roles.gestionar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/seguridad`.  
- Todas las respuestas son JSON.  
- Los listados son paginados con `per_page`.  
- Todas las operaciones requieren autenticación Sanctum.  
- `usuarios` usa soft delete.  
- La asignación de roles a usuarios se resuelve por `usuarios_roles`.  
- Los permisos efectivos de un usuario se calculan por sus roles activos en `usuarios_roles` y los permisos asignados en `roles_permisos`.

## 1. Usuarios

Representa usuarios internos con acceso autenticado al backend.

### Rutas

GET    /api/seguridad/usuarios

POST   /api/seguridad/usuarios

GET    /api/seguridad/usuarios/{usuario}

PUT    /api/seguridad/usuarios/{usuario}

DELETE /api/seguridad/usuarios/{usuario}

### Request `POST/PUT`

{

  "area\_municipal\_id": 1,

  "nombre\_usuario": "mlopez",

  "nombre": "Maria",

  "apellido": "Lopez",

  "email": "mlopez@lujan.gob.ar",

  "password": "12345678",

  "activo": true

}

### Response `show`

{

  "id": 2,

  "area\_municipal\_id": 1,

  "nombre\_usuario": "mlopez",

  "nombre": "Maria",

  "apellido": "Lopez",

  "email": "mlopez@lujan.gob.ar",

  "activo": true,

  "ultimo\_acceso\_at": "2026-04-29T12:00:00.000000Z",

  "roles": [],

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

## 2. Roles

Representa perfiles funcionales o administrativos agrupados por permisos.

### Rutas

GET    /api/seguridad/roles

POST   /api/seguridad/roles

GET    /api/seguridad/roles/{rol}

PUT    /api/seguridad/roles/{rol}

DELETE /api/seguridad/roles/{rol}

### Request `POST/PUT`

{

  "codigo": "RESPONSABLE\_SALUD",

  "nombre": "Responsable Salud",

  "descripcion": "Gestión operativa del area de salud",

  "activo": true

}

### Response `show`

{

  "id": 7,

  "codigo": "RESPONSABLE\_SALUD",

  "nombre": "Responsable Salud",

  "descripcion": "Gestión operativa del area de salud",

  "activo": true,

  "permisos": [],

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

## 3. Asignacion De Roles A Usuarios

Resuelve la vinculación many-to-many entre usuarios y roles.

### Rutas

GET    /api/seguridad/usuarios/{usuario}/roles

POST   /api/seguridad/usuarios/{usuario}/roles

DELETE /api/seguridad/usuarios/{usuario}/roles/{rol}

### Request `POST`

{

  "rol\_id": 7

}

### Response `GET`

{

  "data": [

    {

      "id": 7,

      "codigo": "RESPONSABLE\_SALUD",

      "nombre": "Responsable Salud"

    }

  ]

}

## 4. Asignación De Permisos A Roles

Resuelve la vinculación many-to-many entre roles y permisos.

### Rutas

GET    /api/seguridad/roles/{rol}/permisos

POST   /api/seguridad/roles/{rol}/permisos

DELETE /api/seguridad/roles/{rol}/permisos/{permiso}

### Request `POST`

{

  "permiso\_id": 11

}

## 5. Permisos Efectivos De Usuario

Permite inspeccionar el conjunto real de permisos resultante de todos los roles asignados a un usuario.

### Rutas

GET /api/seguridad/usuarios/{usuario}/permisos-efectivos

### Response

{

  "usuario\_id": 2,

  "permisos": [

    {

      "id": 11,

      "codigo": "datos\_fuente.ver",

      "nombre": "Ver datos fuente"

    },

    {

      "id": 13,

      "codigo": "datos\_fuente.cargar",

      "nombre": "Cargar datos fuente"

    }

  ]

}

## Filtros Propuestos

### Usuarios

GET /api/seguridad/usuarios?per\_page=15\&activo=true\&area\_municipal\_id=1

Filtros:

- `per_page`  
- `activo`  
- `area_municipal_id`  
- `nombre_usuario`  
- `email`  
- `nombre`  
- `apellido`

### Roles

GET /api/seguridad/roles?per\_page=15\&activo=true

Filtros:

- `per_page`  
- `activo`  
- `codigo`  
- `nombre`

## Reglas De Negocio A Respetar

1. `usuarios.nombre_usuario` debe ser único.  
2. `usuarios.email` debe ser único.  
3. `roles.codigo` debe ser único.  
4. `roles.nombre` debe ser único.  
5. No se debe devolver `password` en responses.  
6. La baja de usuario debe ser lógica, no física.  
7. La dupla `usuario_id + rol_id` debe mantenerse única.  
8. La dupla `rol_id + permiso_id` debe mantenerse única.  
9. El login sigue usando `nombre_usuario` y no se rediseña en este bloque.  
10. Los permisos efectivos se calculan por roles, no por asignación directa al usuario.

## Decisiones Operativas Para La Implementación

1. El módulo se expone bajo `/api/seguridad`.  
2. La administración de usuarios requiere `usuarios.gestionar`.  
3. La administración de roles y permisos requiere `roles.gestionar`.  
4. La asignación de roles a usuarios se resuelve con endpoints propios y no embebida obligatoriamente en el payload del usuario.  
5. La consulta de permisos efectivos se expone como endpoint de solo lectura para diagnóstico operativo.
