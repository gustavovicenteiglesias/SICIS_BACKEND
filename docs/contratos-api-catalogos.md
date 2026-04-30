# Contratos API \- Catálogos Base

Este documento define el contrato inicial del ABM de catálogos del MVP backend y refleja la primera implementación del bloque prioritario.

## Alcance Inicial

Catálogos prioritarios:

1. Categorías.  
2. Categorías temáticas.  
3. Unidades de medida.  
4. Periodicidades.  
5. Estados de dato.  
6. Estados de corrida.  
7. Estados de resultado.  
8. Modalidades de carga.

Catálogos secundarios para una segunda tanda:

1. Áreas municipales.  
2. Fuentes institucionales.  
3. Tipos de indicador.  
4. Normas.  
5. Tipos de jurisdicción.  
6. Jurisdicciones.

## Catálogos Secundarios Implementados

### Áreas Municipales

GET    /api/catalogos/areas-municipales

POST   /api/catalogos/areas-municipales

GET    /api/catalogos/areas-municipales/{id}

PUT    /api/catalogos/areas-municipales/{id}

DELETE /api/catalogos/areas-municipales/{id}

### Fuentes Institucionales

GET    /api/catalogos/fuentes-institucionales

POST   /api/catalogos/fuentes-institucionales

GET    /api/catalogos/fuentes-institucionales/{id}

PUT    /api/catalogos/fuentes-institucionales/{id}

DELETE /api/catalogos/fuentes-institucionales/{id}

### Tipos De Indicador

GET    /api/catalogos/tipos-indicador

POST   /api/catalogos/tipos-indicador

GET    /api/catalogos/tipos-indicador/{id}

PUT    /api/catalogos/tipos-indicador/{id}

DELETE /api/catalogos/tipos-indicador/{id}

### Normas

GET    /api/catalogos/normas

POST   /api/catalogos/normas

GET    /api/catalogos/normas/{id}

PUT    /api/catalogos/normas/{id}

DELETE /api/catalogos/normas/{id}

### Tipos De Jurisdicción

GET    /api/catalogos/tipos-jurisdiccion

POST   /api/catalogos/tipos-jurisdiccion

GET    /api/catalogos/tipos-jurisdiccion/{id}

PUT    /api/catalogos/tipos-jurisdiccion/{id}

DELETE /api/catalogos/tipos-jurisdiccion/{id}

### Jurisdicciones

GET    /api/catalogos/jurisdicciones

POST   /api/catalogos/jurisdicciones

GET    /api/catalogos/jurisdicciones/{id}

PUT    /api/catalogos/jurisdicciones/{id}

DELETE /api/catalogos/jurisdicciones/{id}

Request `POST/PUT`:

{

  "tipo\_jurisdiccion\_id": 3,

  "jurisdiccion\_padre\_id": 2,

  "nombre": "Luján",

  "codigo\_oficial": "LUJ",

  "latitud": \-34.5702778,

  "longitud": \-59.105,

  "activa": true

}

## Convenciones Generales

- Todas las rutas son bajo `/api`.  
- Todas las rutas de escritura requieren autenticación Sanctum.  
- Las respuestas devuelven JSON.  
- Los listados deben soportar filtros simples por `activo`/`activa`, `codigo`, `nombre` cuando aplique.  
- Los listados deben soportar paginación con `per_page`.  
- No se eliminan registros fisicamente si la tabla tiene `deleted_at`; se usa soft delete.  
- Los errores de validación usan respuesta Laravel `422`.  
- Acceso no autenticado a rutas protegidas devuelve `401`.  
- Acceso sin permisos devuelve `403`.

## Permisos Propuestos

- Lectura de catálogos: `indicadores.ver`.  
- Escritura de catálogos metodológicos: `indicadores.configurar`.  
- Gestión de usuarios, roles y permisos: queda fuera de este bloque.

Roles sugeridos:

- `ADMINISTRADOR_GENERAL`: lectura y escritura.  
- `GESTOR_INDICADORES`: lectura y escritura de catálogos del MVP.  
- `AUDITOR_INTERNO`: lectura.  
- `CARGADOR_DATOS`: lectura.  
- `DECISOR`: lectura.

## Rutas Propuestas

### Categorías

GET    /api/catalogos/categorias

POST   /api/catalogos/categorias

GET    /api/catalogos/categorias/{categoria}

PUT    /api/catalogos/categorias/{categoria}

DELETE /api/catalogos/categorias/{categoria}

Request `POST/PUT`:

{

  "nombre": "Salud",

  "descripcion": "Indicadores sanitarios",

  "orden": 5,

  "activa": true

}

### Categorías Temáticas

GET    /api/catalogos/categorias-tematicas

POST   /api/catalogos/categorias-tematicas

GET    /api/catalogos/categorias-tematicas/{categoria\_tematica}

PUT    /api/catalogos/categorias-tematicas/{categoria\_tematica}

DELETE /api/catalogos/categorias-tematicas/{categoria\_tematica}

Request `POST/PUT`:

{

  "categoria\_id": 5,

  "nombre": "Atencion primaria",

  "descripcion": "Indicadores de atencion primaria",

  "orden": 1,

  "activa": true

}

### Unidades De Medida

GET    /api/catalogos/unidades-medida

POST   /api/catalogos/unidades-medida

GET    /api/catalogos/unidades-medida/{unidad\_medida}

PUT    /api/catalogos/unidades-medida/{unidad\_medida}

DELETE /api/catalogos/unidades-medida/{unidad\_medida}

Request `POST/PUT`:

{

  "nombre": "porcentaje",

  "simbolo": "%",

  "descripcion": "Relación porcentual"

}

### Periodicidades

GET    /api/catalogos/periodicidades

POST   /api/catalogos/periodicidades

GET    /api/catalogos/periodicidades/{periodicidad}

PUT    /api/catalogos/periodicidades/{periodicidad}

DELETE /api/catalogos/periodicidades/{periodicidad}

Request `POST/PUT`:

{

  "codigo": "ANUAL",

  "nombre": "Anual",

  "descripcion": "Una medicion por anio"

}

### Estados Y Modalidades

Para estados y modalidades se implementa el mismo patrón CRUD:

GET    /api/catalogos/estados-dato

POST   /api/catalogos/estados-dato

GET    /api/catalogos/estados-dato/{estado\_dato}

PUT    /api/catalogos/estados-dato/{estado\_dato}

DELETE /api/catalogos/estados-dato/{estado\_dato}

GET    /api/catalogos/estados-corrida

POST   /api/catalogos/estados-corrida

GET    /api/catalogos/estados-corrida/{estado\_corrida}

PUT    /api/catalogos/estados-corrida/{estado\_corrida}

DELETE /api/catalogos/estados-corrida/{estado\_corrida}

GET    /api/catalogos/estados-resultado

POST   /api/catalogos/estados-resultado

GET    /api/catalogos/estados-resultado/{estado\_resultado}

PUT    /api/catalogos/estados-resultado/{estado\_resultado}

DELETE /api/catalogos/estados-resultado/{estado\_resultado}

GET    /api/catalogos/modalidades-carga

POST   /api/catalogos/modalidades-carga

GET    /api/catalogos/modalidades-carga/{modalidad\_carga}

PUT    /api/catalogos/modalidades-carga/{modalidad\_carga}

DELETE /api/catalogos/modalidades-carga/{modalidad\_carga}

Request `POST/PUT`:

{

  "codigo": "VALIDADO",

  "nombre": "Validado",

  "descripcion": "Dato validado para corridas"

}

## Shape De Respuesta

Item:

{

  "id": 1,

  "codigo": "ANUAL",

  "nombre": "Anual",

  "descripcion": "Una medicion por anio",

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

Listado paginado:

{

  "data": [],

  "links": {},

  "meta": {}

}

## Decision Tomada En Esta Primera Implementación

- Los catálogos prioritarios del bloque se exponen con CRUD completo.  
- Los catálogos secundarios del bloque también se exponen con CRUD completo.  
- `DELETE` usa soft delete cuando el modelo lo soporta y delete físico cuando no.  
- La autorización se resuelve con middleware `permission` apoyado en roles y permisos.  
- `jurisdicciones` valida que no pueda apuntarse a sí misma como padre.
