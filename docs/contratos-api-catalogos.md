# Contratos API - Catalogos Base

Este documento define el contrato inicial del ABM de catalogos del MVP backend y refleja la primera implementacion del bloque prioritario.

## Alcance Inicial

Catalogos prioritarios:

1. Categorias.
2. Categorias tematicas.
3. Unidades de medida.
4. Periodicidades.
5. Estados de dato.
6. Estados de corrida.
7. Estados de resultado.
8. Modalidades de carga.

Catalogos secundarios para una segunda tanda:

1. Areas municipales.
2. Fuentes institucionales.
3. Tipos de indicador.
4. Normas.
5. Tipos de jurisdiccion.
6. Jurisdicciones.

## Catalogos Secundarios Implementados

### Areas Municipales

```http
GET    /api/catalogos/areas-municipales
POST   /api/catalogos/areas-municipales
GET    /api/catalogos/areas-municipales/{id}
PUT    /api/catalogos/areas-municipales/{id}
DELETE /api/catalogos/areas-municipales/{id}
```

### Fuentes Institucionales

```http
GET    /api/catalogos/fuentes-institucionales
POST   /api/catalogos/fuentes-institucionales
GET    /api/catalogos/fuentes-institucionales/{id}
PUT    /api/catalogos/fuentes-institucionales/{id}
DELETE /api/catalogos/fuentes-institucionales/{id}
```

### Tipos De Indicador

```http
GET    /api/catalogos/tipos-indicador
POST   /api/catalogos/tipos-indicador
GET    /api/catalogos/tipos-indicador/{id}
PUT    /api/catalogos/tipos-indicador/{id}
DELETE /api/catalogos/tipos-indicador/{id}
```

### Normas

```http
GET    /api/catalogos/normas
POST   /api/catalogos/normas
GET    /api/catalogos/normas/{id}
PUT    /api/catalogos/normas/{id}
DELETE /api/catalogos/normas/{id}
```

### Tipos De Jurisdiccion

```http
GET    /api/catalogos/tipos-jurisdiccion
POST   /api/catalogos/tipos-jurisdiccion
GET    /api/catalogos/tipos-jurisdiccion/{id}
PUT    /api/catalogos/tipos-jurisdiccion/{id}
DELETE /api/catalogos/tipos-jurisdiccion/{id}
```

### Jurisdicciones

```http
GET    /api/catalogos/jurisdicciones
POST   /api/catalogos/jurisdicciones
GET    /api/catalogos/jurisdicciones/{id}
PUT    /api/catalogos/jurisdicciones/{id}
DELETE /api/catalogos/jurisdicciones/{id}
```

Request `POST/PUT`:

```json
{
  "tipo_jurisdiccion_id": 3,
  "jurisdiccion_padre_id": 2,
  "nombre": "Lujan",
  "codigo_oficial": "LUJ",
  "latitud": -34.5702778,
  "longitud": -59.105,
  "activa": true
}
```

## Convenciones Generales

- Todas las rutas son bajo `/api`.
- Todas las rutas de escritura requieren autenticacion Sanctum.
- Las respuestas devuelven JSON.
- Los listados deben soportar filtros simples por `activo`/`activa`, `codigo`, `nombre` cuando aplique.
- Los listados deben soportar paginacion con `per_page`.
- No se eliminan registros fisicamente si la tabla tiene `deleted_at`; se usa soft delete.
- Los errores de validacion usan respuesta Laravel `422`.
- Acceso no autenticado a rutas protegidas devuelve `401`.
- Acceso sin permisos devuelve `403`.

## Permisos Propuestos

- Lectura de catalogos: `indicadores.ver`.
- Escritura de catalogos metodologicos: `indicadores.configurar`.
- Gestion de usuarios, roles y permisos: queda fuera de este bloque.

Roles sugeridos:

- `ADMINISTRADOR_GENERAL`: lectura y escritura.
- `GESTOR_INDICADORES`: lectura y escritura de catalogos del MVP.
- `AUDITOR_INTERNO`: lectura.
- `CARGADOR_DATOS`: lectura.
- `DECISOR`: lectura.

## Rutas Propuestas

### Categorias

```http
GET    /api/catalogos/categorias
POST   /api/catalogos/categorias
GET    /api/catalogos/categorias/{categoria}
PUT    /api/catalogos/categorias/{categoria}
DELETE /api/catalogos/categorias/{categoria}
```

Request `POST/PUT`:

```json
{
  "nombre": "Salud",
  "descripcion": "Indicadores sanitarios",
  "orden": 5,
  "activa": true
}
```

### Categorias Tematicas

```http
GET    /api/catalogos/categorias-tematicas
POST   /api/catalogos/categorias-tematicas
GET    /api/catalogos/categorias-tematicas/{categoria_tematica}
PUT    /api/catalogos/categorias-tematicas/{categoria_tematica}
DELETE /api/catalogos/categorias-tematicas/{categoria_tematica}
```

Request `POST/PUT`:

```json
{
  "categoria_id": 5,
  "nombre": "Atencion primaria",
  "descripcion": "Indicadores de atencion primaria",
  "orden": 1,
  "activa": true
}
```

### Unidades De Medida

```http
GET    /api/catalogos/unidades-medida
POST   /api/catalogos/unidades-medida
GET    /api/catalogos/unidades-medida/{unidad_medida}
PUT    /api/catalogos/unidades-medida/{unidad_medida}
DELETE /api/catalogos/unidades-medida/{unidad_medida}
```

Request `POST/PUT`:

```json
{
  "nombre": "porcentaje",
  "simbolo": "%",
  "descripcion": "Relacion porcentual"
}
```

### Periodicidades

```http
GET    /api/catalogos/periodicidades
POST   /api/catalogos/periodicidades
GET    /api/catalogos/periodicidades/{periodicidad}
PUT    /api/catalogos/periodicidades/{periodicidad}
DELETE /api/catalogos/periodicidades/{periodicidad}
```

Request `POST/PUT`:

```json
{
  "codigo": "ANUAL",
  "nombre": "Anual",
  "descripcion": "Una medicion por anio"
}
```

### Estados Y Modalidades

Para estados y modalidades se implementa el mismo patron CRUD:

```http
GET    /api/catalogos/estados-dato
POST   /api/catalogos/estados-dato
GET    /api/catalogos/estados-dato/{estado_dato}
PUT    /api/catalogos/estados-dato/{estado_dato}
DELETE /api/catalogos/estados-dato/{estado_dato}

GET    /api/catalogos/estados-corrida
POST   /api/catalogos/estados-corrida
GET    /api/catalogos/estados-corrida/{estado_corrida}
PUT    /api/catalogos/estados-corrida/{estado_corrida}
DELETE /api/catalogos/estados-corrida/{estado_corrida}

GET    /api/catalogos/estados-resultado
POST   /api/catalogos/estados-resultado
GET    /api/catalogos/estados-resultado/{estado_resultado}
PUT    /api/catalogos/estados-resultado/{estado_resultado}
DELETE /api/catalogos/estados-resultado/{estado_resultado}

GET    /api/catalogos/modalidades-carga
POST   /api/catalogos/modalidades-carga
GET    /api/catalogos/modalidades-carga/{modalidad_carga}
PUT    /api/catalogos/modalidades-carga/{modalidad_carga}
DELETE /api/catalogos/modalidades-carga/{modalidad_carga}
```

Request `POST/PUT`:

```json
{
  "codigo": "VALIDADO",
  "nombre": "Validado",
  "descripcion": "Dato validado para corridas"
}
```

## Shape De Respuesta

Item:

```json
{
  "id": 1,
  "codigo": "ANUAL",
  "nombre": "Anual",
  "descripcion": "Una medicion por anio",
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

Listado paginado:

```json
{
  "data": [],
  "links": {},
  "meta": {}
}
```

## Decision Tomada En Esta Primera Implementacion

- Los catalogos prioritarios del bloque se exponen con CRUD completo.
- Los catalogos secundarios del bloque tambien se exponen con CRUD completo.
- `DELETE` usa soft delete cuando el modelo lo soporta y delete fisico cuando no.
- La autorizacion se resuelve con middleware `permission` apoyado en roles y permisos.
- `jurisdicciones` valida que no pueda apuntarse a si misma como padre.
