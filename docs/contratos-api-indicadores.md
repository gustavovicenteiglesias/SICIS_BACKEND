# Contratos API - Indicadores

Este documento define el contrato propuesto para el modulo de indicadores del backend SICIS. Cubre el catalogo principal de indicadores, el versionado metodologico y las variables asociadas a cada version.

## Alcance

Este bloque cubre:

1. `indicadores`
2. `indicadores_normas`
3. `indicadores_versiones`
4. `indicadores_variables`

Quedan fuera de este documento:

1. calculo de corridas
2. publicacion de resultados
3. carga de datos fuente

## Permisos Propuestos

- Lectura: `indicadores.ver`
- Escritura: `indicadores.configurar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/indicadores`.
- Todas las respuestas son JSON.
- Los listados son paginados con `per_page`.
- Los listados admiten filtros por `activo`, `publicable`, `sensible`, `categoria_id`, `categoria_tematica_id`, `codigo_interno` y `nombre` cuando corresponda.
- Las operaciones de escritura requieren autenticacion Sanctum y permiso de configuracion.
- `DELETE` usa soft delete cuando el modelo lo soporta.

## 1. Indicadores

Representa el catalogo funcional estable del indicador.

### Rutas

```http
GET    /api/indicadores
POST   /api/indicadores
GET    /api/indicadores/{indicador}
PUT    /api/indicadores/{indicador}
DELETE /api/indicadores/{indicador}
```

### Request `POST/PUT`

```json
{
  "codigo_interno": "SALUD_001",
  "categoria_id": 5,
  "categoria_tematica_id": 12,
  "nombre": "Cobertura de vacunacion infantil",
  "descripcion": "Porcentaje de poblacion infantil con esquema completo",
  "publicable": true,
  "sensible": false,
  "activo": true,
  "orden": 1,
  "normas": [
    {
      "norma_id": 1,
      "codigo_en_norma": "H1",
      "nombre_en_norma": "Cobertura de vacunacion"
    }
  ]
}
```

### Response `show`

```json
{
  "id": 1,
  "codigo_interno": "SALUD_001",
  "categoria_id": 5,
  "categoria_tematica_id": 12,
  "nombre": "Cobertura de vacunacion infantil",
  "descripcion": "Porcentaje de poblacion infantil con esquema completo",
  "publicable": true,
  "sensible": false,
  "activo": true,
  "orden": 1,
  "categoria": {},
  "categoria_tematica": {},
  "normas": [],
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

## 2. Versiones Metodologicas

Cada indicador puede tener una o mas versiones metodologicas.

### Rutas

```http
GET    /api/indicadores/{indicador}/versiones
POST   /api/indicadores/{indicador}/versiones
GET    /api/indicadores/{indicador}/versiones/{version}
PUT    /api/indicadores/{indicador}/versiones/{version}
DELETE /api/indicadores/{indicador}/versiones/{version}
```

### Request `POST/PUT`

```json
{
  "tipo_indicador_id": 1,
  "unidad_medida_id": 2,
  "periodicidad_id": 1,
  "version": "2026.1",
  "formula_tipo": "RATIO_CONSTANTE",
  "constante": 100.0,
  "formula_texto": "(vacunados / poblacion_objetivo) * 100",
  "formula_expression": "(A / B) * 100",
  "objetivo": "Medir cobertura de vacunacion",
  "observaciones_metodologicas": "Se calcula sobre padrón validado",
  "vigente_desde": "2026-01-01",
  "vigente_hasta": null,
  "activa": true
}
```

### Response `show`

```json
{
  "id": 7,
  "indicador_id": 1,
  "tipo_indicador_id": 1,
  "unidad_medida_id": 2,
  "periodicidad_id": 1,
  "version": "2026.1",
  "formula_tipo": "RATIO_CONSTANTE",
  "constante": "100.000000",
  "formula_texto": "(vacunados / poblacion_objetivo) * 100",
  "formula_expression": "(A / B) * 100",
  "objetivo": "Medir cobertura de vacunacion",
  "observaciones_metodologicas": "Se calcula sobre padrón validado",
  "vigente_desde": "2026-01-01",
  "vigente_hasta": null,
  "activa": true,
  "tipo_indicador": {},
  "unidad_medida": {},
  "periodicidad": {},
  "variables": []
}
```

## 3. Variables De Indicador

Define que datos fuente utiliza una version metodologica.

### Rutas

```http
GET    /api/indicadores/{indicador}/versiones/{version}/variables
POST   /api/indicadores/{indicador}/versiones/{version}/variables
GET    /api/indicadores/{indicador}/versiones/{version}/variables/{variable}
PUT    /api/indicadores/{indicador}/versiones/{version}/variables/{variable}
DELETE /api/indicadores/{indicador}/versiones/{version}/variables/{variable}
```

### Request `POST/PUT`

```json
{
  "dato_fuente_id": 9,
  "codigo_variable": "A",
  "rol": "NUMERADOR",
  "obligatorio": true,
  "orden": 1,
  "descripcion": "Cantidad de vacunados"
}
```

### Response `show`

```json
{
  "id": 14,
  "indicador_version_id": 7,
  "dato_fuente_id": 9,
  "codigo_variable": "A",
  "rol": "NUMERADOR",
  "obligatorio": true,
  "orden": 1,
  "descripcion": "Cantidad de vacunados",
  "dato_fuente": {}
}
```

## Filtros Propuestos

### Indicadores

```http
GET /api/indicadores?per_page=15&activo=true&categoria_id=5&publicable=true
```

Filtros:

- `per_page`
- `activo`
- `publicable`
- `sensible`
- `categoria_id`
- `categoria_tematica_id`
- `codigo_interno`
- `nombre`

### Versiones

```http
GET /api/indicadores/{indicador}/versiones?activa=true
```

Filtros:

- `activa`
- `version`
- `tipo_indicador_id`
- `unidad_medida_id`
- `periodicidad_id`
- `vigente_desde`
- `vigente_hasta`

### Variables

```http
GET /api/indicadores/{indicador}/versiones/{version}/variables?dato_fuente_id=9
```

Filtros:

- `dato_fuente_id`
- `codigo_variable`
- `rol`
- `obligatorio`

## Reglas De Negocio A Respetar

1. `indicadores.codigo_interno` debe ser unico.
2. La dupla `indicador_id + version` debe ser unica.
3. La dupla `indicador_version_id + codigo_variable` debe ser unica.
4. Una variable debe apuntar a un `dato_fuente` existente.
5. La version metodologica debe quedar ligada a `tipo_indicador`, `unidad_medida` y `periodicidad`.
6. Si un indicador se desactiva, sus versiones no deberian quedar publicadas como vigentes.
7. Las normas se administran dentro del payload del indicador.
8. Las variables se administran con endpoints propios bajo cada version.
9. No se permiten versiones activas con periodos de vigencia superpuestos para un mismo indicador.
