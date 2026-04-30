# Contratos API \- Datos Fuente

Este documento define el contrato propuesto para el módulo de datos fuente del backend SICIS. Cubre el catálogo de datos fuente, la carga y validación de valores, las evidencias asociadas y la configuración de consumo de APIs externas.

## Alcance

Este bloque cubre:

1. `datos_fuente`  
2. `datos_fuente_valores`  
3. `evidencias_dato`  
4. `datos_fuente_api_configs`  
5. `datos_fuente_api_paths`  
6. `datos_fuente_api_importaciones`

Quedan fuera de este documento:

1. cálculo de corridas  
2. publicación de resultados  
3. reglas de snapshot histórico

## Permisos Propuestos

- Lectura general: `datos_fuente.ver`  
- Configuración de catálogo y conectores: `datos_fuente.configurar`  
- Carga manual de valores: `datos_fuente.cargar`  
- Validación y cierre de valores: `datos_fuente.validar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/datos-fuente`.  
- Todas las respuestas son JSON.  
- Los listados son paginados con `per_page`.  
- Las operaciones de escritura requieren autenticación Sanctum.  
- `DELETE` usa soft delete cuando el modelo lo soporta.  
- Los valores cargados manualmente se crean con `modalidad_carga_id` y `usuario_carga_id`.  
- La validación de datos se modela sobre `estado_dato_id`, `usuario_valida_id` y `validado_at`.

## 1. Datos Fuente

Representa la definición estable del dato operativo que luego puede ser utilizado por indicadores o corridas.

### Rutas

GET    /api/datos-fuente

POST   /api/datos-fuente

GET    /api/datos-fuente/{datoFuente}

PUT    /api/datos-fuente/{datoFuente}

DELETE /api/datos-fuente/{datoFuente}

### Request `POST/PUT`

{

  "codigo\_interno": "VACUNADOS\_MENSUAL",

  "area\_municipal\_id": 3,

  "unidad\_medida\_id": 2,

  "periodicidad\_id": 1,

  "modalidad\_carga\_id": 1,

  "fuente\_institucional\_id": 4,

  "responsable\_usuario\_id": 1,

  "nombre": "Cantidad mensual de vacunados",

  "descripcion": "Total de personas vacunadas por mes",

  "tipo\_dato": "decimal",

  "metodo\_obtencion": "Consolidacion desde sistema nominal",

  "link\_fuente": "https://ejemplo.gob.ar/fuente",

  "rango\_minimo": 0,

  "rango\_maximo": 999999,

  "nivel\_geografico": "MUNICIPAL",

  "activo": true

}

### Response `show`

{

  "id": 9,

  "codigo\_interno": "VACUNADOS\_MENSUAL",

  "area\_municipal\_id": 3,

  "unidad\_medida\_id": 2,

  "periodicidad\_id": 1,

  "modalidad\_carga\_id": 1,

  "fuente\_institucional\_id": 4,

  "responsable\_usuario\_id": 1,

  "nombre": "Cantidad mensual de vacunados",

  "descripcion": "Total de personas vacunadas por mes",

  "tipo\_dato": "decimal",

  "metodo\_obtencion": "Consolidacion desde sistema nominal",

  "link\_fuente": "https://ejemplo.gob.ar/fuente",

  "rango\_minimo": "0.000000",

  "rango\_maximo": "999999.000000",

  "nivel\_geografico": "MUNICIPAL",

  "activo": true,

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

### Request `POST /importar`

Cuando la API externa no devuelve un periodo o una jurisdicción resoluble, el endpoint admite fallbacks por request:

{

  "jurisdiccion\_id": 3,

  "periodo\_referencia": "2026-03-01",

  "fecha\_produccion": "2026-03-31",

  "estado\_dato\_id": 2,

  "vigente": true

}

## 2. Valores De Datos Fuente

Representa cada observacion cargada o importada para un dato fuente, una jurisdicción y un periodo.

### Rutas

GET    /api/datos-fuente/{datoFuente}/valores

POST   /api/datos-fuente/{datoFuente}/valores

GET    /api/datos-fuente/{datoFuente}/valores/{valor}

PUT    /api/datos-fuente/{datoFuente}/valores/{valor}

POST   /api/datos-fuente/{datoFuente}/valores/{valor}/validar

DELETE /api/datos-fuente/{datoFuente}/valores/{valor}

### Request `POST/PUT`

{

  "jurisdiccion\_id": 1,

  "estado\_dato\_id": 1,

  "modalidad\_carga\_id": 1,

  "valor\_crudo": 15234.75,

  "valor\_utilizado": 15234.75,

  "periodo\_referencia": "2026-03-01",

  "fecha\_produccion": "2026-03-31",

  "observado\_motivo": null,

  "vigente": true

}

### Request `POST /validar`

{

  "estado\_dato\_id": 2,

  "valor\_utilizado": 15234.75,

  "observado\_motivo": null

}

### Response `show`

{

  "id": 55,

  "dato\_fuente\_id": 9,

  "jurisdiccion\_id": 1,

  "estado\_dato\_id": 2,

  "modalidad\_carga\_id": 1,

  "usuario\_carga\_id": 1,

  "usuario\_valida\_id": 2,

  "valor\_crudo": "15234.750000",

  "valor\_utilizado": "15234.750000",

  "periodo\_referencia": "2026-03-01",

  "fecha\_produccion": "2026-03-31",

  "fecha\_carga": "2026-04-29T12:00:00.000000Z",

  "validado\_at": "2026-04-29T13:00:00.000000Z",

  "observado\_motivo": null,

  "vigente": true,

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T13:00:00.000000Z",

  "evidencias": []

}

## 3. Evidencias De Dato

Representa archivos o referencias externas que respaldan un valor cargado.

### Rutas

GET    /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias

POST   /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias

GET    /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias/{evidencia}

PUT    /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias/{evidencia}

DELETE /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias/{evidencia}

### Request `POST/PUT`

{

  "nombre\_archivo": "vacunacion\_marzo\_2026.pdf",

  "url": "https://storage.ejemplo.local/evidencias/vacunacion\_marzo\_2026.pdf",

  "hash\_archivo": "sha256:abc123",

  "descripcion": "Reporte consolidado firmado por el area"

}

### Response `show`

{

  "id": 101,

  "dato\_fuente\_valor\_id": 55,

  "nombre\_archivo": "vacunacion\_marzo\_2026.pdf",

  "url": "https://storage.ejemplo.local/evidencias/vacunacion\_marzo\_2026.pdf",

  "hash\_archivo": "sha256:abc123",

  "descripcion": "Reporte consolidado firmado por el area",

  "usuario\_id": 1,

  "created\_at": "2026-04-29T12:10:00.000000Z",

  "updated\_at": "2026-04-29T12:10:00.000000Z"

}

## 4. Configuración API Por Dato Fuente

Representa la configuración de una fuente HTTP desde la que se puede importar un dato.

### Rutas

GET    /api/datos-fuente/{datoFuente}/api-configs

POST   /api/datos-fuente/{datoFuente}/api-configs

GET    /api/datos-fuente/{datoFuente}/api-configs/{config}

PUT    /api/datos-fuente/{datoFuente}/api-configs/{config}

DELETE /api/datos-fuente/{datoFuente}/api-configs/{config}

POST   /api/datos-fuente/{datoFuente}/api-configs/{config}/probar

POST   /api/datos-fuente/{datoFuente}/api-configs/{config}/importar

GET    /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones

### Request `POST/PUT`

{

  "nombre": "API Vacunacion Provincial",

  "metodo\_http": "GET",

  "url": "https://api.ejemplo.gob.ar/v1/vacunacion",

  "auth\_tipo": "BEARER",

  "headers\_json": {

    "Authorization": "Bearer {{TOKEN}}"

  },

  "params\_json": {

    "jurisdiccion": "LUJAN"

  },

  "json\_path\_valor": "data.total",

  "json\_path\_periodo": "data.periodo",

  "json\_path\_jurisdiccion": "data.jurisdiccion",

  "unidad\_esperada": "personas",

  "activo": true

}

### Response `show`

{

  "id": 4,

  "dato\_fuente\_id": 9,

  "nombre": "API Vacunacion Provincial",

  "metodo\_http": "GET",

  "url": "https://api.ejemplo.gob.ar/v1/vacunacion",

  "auth\_tipo": "BEARER",

  "headers\_json": {

    "Authorization": "Bearer {{TOKEN}}"

  },

  "params\_json": {

    "jurisdiccion": "LUJAN"

  },

  "json\_path\_valor": "data.total",

  "json\_path\_periodo": "data.periodo",

  "json\_path\_jurisdiccion": "data.jurisdiccion",

  "unidad\_esperada": "personas",

  "activo": true,

  "paths": [],

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

## 5. Paths Alternativos De Extracción

Representa alternativas ordenadas de `json_path_valor` para manejar cambios menores del proveedor externo.

### Rutas

GET    /api/datos-fuente/{datoFuente}/api-configs/{config}/paths

POST   /api/datos-fuente/{datoFuente}/api-configs/{config}/paths

PUT    /api/datos-fuente/{datoFuente}/api-configs/{config}/paths/{path}

DELETE /api/datos-fuente/{datoFuente}/api-configs/{config}/paths/{path}

### Request `POST/PUT`

{

  "json\_path\_valor": "result.valor",

  "prioridad": 2,

  "activo": true

}

## 6. Historial De Importaciones API

Representa la auditoría operativa de cada intento de consulta/importacion externa.

### Rutas

GET /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones

GET /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones/{importacion}

### Response `show`

{

  "id": 88,

  "dato\_fuente\_api\_config\_id": 4,

  "fecha\_importacion": "2026-04-29T12:00:00.000000Z",

  "estado": "OK",

  "http\_status": 200,

  "json\_path\_usado": "data.total",

  "valor\_extraido": "15234.750000",

  "mensaje\_error": null,

  "muestra\_respuesta": {

    "data": {

      "total": 15234.75

    }

  },

  "created\_at": "2026-04-29T12:00:00.000000Z",

  "updated\_at": "2026-04-29T12:00:00.000000Z"

}

## Filtros Propuestos

### Datos Fuente

GET /api/datos-fuente?per\_page=15\&activo=true\&modalidad\_carga\_id=1

Filtros:

- `per_page`  
- `activo`  
- `codigo_interno`  
- `nombre`  
- `area_municipal_id`  
- `unidad_medida_id`  
- `periodicidad_id`  
- `modalidad_carga_id`  
- `fuente_institucional_id`  
- `responsable_usuario_id`

### Valores

GET /api/datos-fuente/{datoFuente}/valores?jurisdiccion\_id=1\&periodo\_referencia=2026-03-01\&vigente=true

Filtros:

- `per_page`  
- `jurisdiccion_id`  
- `estado_dato_id`  
- `modalidad_carga_id`  
- `usuario_carga_id`  
- `usuario_valida_id`  
- `periodo_referencia`  
- `fecha_produccion`  
- `vigente`

### Configuraciones API

GET /api/datos-fuente/{datoFuente}/api-configs?activo=true

Filtros:

- `per_page`  
- `activo`  
- `metodo_http`  
- `auth_tipo`

### Importaciones API

GET /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones?estado=ERROR

Filtros:

- `per_page`  
- `estado`  
- `http_status`  
- `fecha_desde`  
- `fecha_hasta`

## Reglas De Negocio A Respetar

1. `datos_fuente.codigo_interno` debe ser unico.  
2. Todo valor debe pertenecer a un `dato_fuente`, una `jurisdiccion`, un `estado_dato` y una `modalidad_carga`.  
3. La carga manual debe registrar `usuario_carga_id` y `fecha_carga`.  
4. La validación debe registrar `usuario_valida_id` y `validado_at`.  
5. Un valor observado debe poder persistir `observado_motivo`.  
6. Las evidencias siempre cuelgan de un valor ya existente.  
7. Una configuración API siempre pertenece a un solo `dato_fuente`.  
8. Los paths alternativos no reemplazan la configuración principal; la complementan con prioridad.  
9. Cada intento de importacion debe quedar auditado en `datos_fuente_api_importaciones`, incluso si falla.  
10. La importacion automática no reemplaza una validación humana; solo genera o actualiza valores según la estrategia operativa que se apruebe en implementacion.

## Decisiones Operativas Para La Implementación

1. El módulo se expone bajo `/api/datos-fuente`.  
2. La validación de valores se modela como acción explícita `POST /validar`.  
3. Las evidencias se gestionan como recurso hijo del valor.  
4. Las configuraciones API y sus paths se gestionan como recursos hijos del `dato_fuente`.  
5. El endpoint `/probar` no persiste valores productivos; solo registra el intento técnico en importaciones cuando se considere necesario.  
6. El endpoint `/importar` puede crear o actualizar valores, pero debe dejar trazabilidad completa del intento.  
7. Si la API no provee jurisdicción o periodo con un path usable, la implementación puede resolverlos con fallbacks enviados por request.
