# Contratos API \- Indicadores

Este documento define el contrato propuesto para el módulo de indicadores del backend SICIS. Cubre el catálogo principal de indicadores, el versionado metodológico y las variables asociadas a cada versión.

## Alcance

Este bloque cubre:

1. `indicadores`  
2. `indicadores_normas`  
3. `indicadores_versiones`  
4. `indicadores_variables`

Quedan fuera de este documento:

1. cálculo de corridas  
2. publicación de resultados  
3. carga de datos fuente

## Permisos Propuestos

- Lectura: `indicadores.ver`  
- Escritura: `indicadores.configurar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/indicadores`.  
- Todas las respuestas son JSON.  
- Los listados son paginados con `per_page`.  
- Los listados admiten filtros por `activo`, `publicable`, `sensible`, `categoria_id`, `categoria_tematica_id`, `codigo_interno` y `nombre` cuando corresponda.  
- Las operaciones de escritura requieren autenticación Sanctum y permiso de configuración.  
- `DELETE` usa soft delete cuando el modelo lo soporta.

## 1. Indicadores

Representa el catálogo funcional estable del indicador.

### Rutas

GET    /api/indicadores

POST   /api/indicadores

GET    /api/indicadores/{indicador}

PUT    /api/indicadores/{indicador}

DELETE /api/indicadores/{indicador}

### Request `POST/PUT`

{

  "codigo\_interno": "SALUD\_001",

  "categoria\_id": 5,

  "categoria\_tematica\_id": 12,

  "nombre": "Cobertura de vacunacion infantil",

  "descripcion": "Porcentaje de poblacion infantil con esquema completo",

  "publicable": true,

  "sensible": false,

  "activo": true,

  "orden": 1,

  "normas": [

    {

      "norma\_id": 1,

      "codigo\_en\_norma": "H1",

      "nombre\_en\_norma": "Cobertura de vacunacion"

    }

  ]

}

### Response `show`

{

  "id": 1,

  "codigo\_interno": "SALUD\_001",

  "categoria\_id": 5,

  "categoria\_tematica\_id": 12,

  "nombre": "Cobertura de vacunacion infantil",

  "descripcion": "Porcentaje de poblacion infantil con esquema completo",

  "publicable": true,

  "sensible": false,

  "activo": true,

  "orden": 1,

  "categoria": {},

  "categoria\_tematica": {},

  "normas": [],

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

## 2. Versiones Metodológicas

Cada indicador puede tener una o más versiones metodológicas.

### Rutas

GET    /api/indicadores/{indicador}/versiones

POST   /api/indicadores/{indicador}/versiones

GET    /api/indicadores/{indicador}/versiones/{version}

PUT    /api/indicadores/{indicador}/versiones/{version}

DELETE /api/indicadores/{indicador}/versiones/{version}

### Request `POST/PUT`

{

  "tipo\_indicador\_id": 1,

  "unidad\_medida\_id": 2,

  "periodicidad\_id": 1,

  "version": "2026.1",

  "formula\_tipo": "RATIO\_CONSTANTE",

  "constante": 100.0,

  "formula\_texto": "(vacunados / poblacion\_objetivo) \* 100",

  "formula\_expression": "(A / B) \* 100",

  "objetivo": "Medir cobertura de vacunacion",

  "observaciones\_metodologicas": "Se calcula sobre padrón validado",

  "vigente\_desde": "2026-01-01",

  "vigente\_hasta": null,

  "activa": true

}

### Response `show`

{

  "id": 7,

  "indicador\_id": 1,

  "tipo\_indicador\_id": 1,

  "unidad\_medida\_id": 2,

  "periodicidad\_id": 1,

  "version": "2026.1",

  "formula\_tipo": "RATIO\_CONSTANTE",

  "constante": "100.000000",

  "formula\_texto": "(vacunados / poblacion\_objetivo) \* 100",

  "formula\_expression": "(A / B) \* 100",

  "objetivo": "Medir cobertura de vacunacion",

  "observaciones\_metodologicas": "Se calcula sobre padrón validado",

  "vigente\_desde": "2026-01-01",

  "vigente\_hasta": null,

  "activa": true,

  "tipo\_indicador": {},

  "unidad\_medida": {},

  "periodicidad": {},

  "variables": []

}

## 3. Variables De Indicador

Define que datos fuente utiliza una versión metodológica.

### Rutas

GET    /api/indicadores/{indicador}/versiones/{version}/variables

POST   /api/indicadores/{indicador}/versiones/{version}/variables

GET    /api/indicadores/{indicador}/versiones/{version}/variables/{variable}

PUT    /api/indicadores/{indicador}/versiones/{version}/variables/{variable}

DELETE /api/indicadores/{indicador}/versiones/{version}/variables/{variable}

### Request `POST/PUT`

{

  "dato\_fuente\_id": 9,

  "codigo\_variable": "A",

  "rol": "NUMERADOR",

  "obligatorio": true,

  "orden": 1,

  "descripcion": "Cantidad de vacunados"

}

### Response `show`

{

  "id": 14,

  "indicador\_version\_id": 7,

  "dato\_fuente\_id": 9,

  "codigo\_variable": "A",

  "rol": "NUMERADOR",

  "obligatorio": true,

  "orden": 1,

  "descripcion": "Cantidad de vacunados",

  "dato\_fuente": {}

}

## Filtros Propuestos

### Indicadores

GET /api/indicadores?per\_page=15\&activo=true\&categoria\_id=5\&publicable=true

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

GET /api/indicadores/{indicador}/versiones?activa=true

Filtros:

- `activa`  
- `version`  
- `tipo_indicador_id`  
- `unidad_medida_id`  
- `periodicidad_id`  
- `vigente_desde`  
- `vigente_hasta`

### Variables

GET /api/indicadores/{indicador}/versiones/{version}/variables?dato\_fuente\_id=9

Filtros:

- `dato_fuente_id`  
- `codigo_variable`  
- `rol`  
- `obligatorio`

## Reglas De Negocio A Respetar

1. `indicadores.codigo_interno` debe ser unico.  
2. La dupla `indicador_id + version` debe ser única.  
3. La dupla `indicador_version_id + codigo_variable` debe ser única.  
4. Una variable debe apuntar a un `dato_fuente` existente.  
5. La versión metodológica debe quedar ligada a `tipo_indicador`, `unidad_medida` y `periodicidad`.  
6. Si un indicador se desactiva, sus versiones no deberían quedar publicadas como vigentes.  
7. Las normas se administran dentro del payload del indicador.  
8. Las variables se administran con endpoints propios bajo cada versión.  
9. No se permiten versiones activas con periodos de vigencia superpuestos para un mismo indicador.
