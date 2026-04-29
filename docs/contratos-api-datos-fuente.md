# Contratos API - Datos Fuente

Este documento define el contrato propuesto para el modulo de datos fuente del backend SICIS. Cubre el catalogo de datos fuente, la carga y validacion de valores, las evidencias asociadas y la configuracion de consumo de APIs externas.

## Alcance

Este bloque cubre:

1. `datos_fuente`
2. `datos_fuente_valores`
3. `evidencias_dato`
4. `datos_fuente_api_configs`
5. `datos_fuente_api_paths`
6. `datos_fuente_api_importaciones`

Quedan fuera de este documento:

1. calculo de corridas
2. publicacion de resultados
3. reglas de snapshot historico

## Permisos Propuestos

- Lectura general: `datos_fuente.ver`
- Configuracion de catalogo y conectores: `datos_fuente.configurar`
- Carga manual de valores: `datos_fuente.cargar`
- Validacion y cierre de valores: `datos_fuente.validar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/datos-fuente`.
- Todas las respuestas son JSON.
- Los listados son paginados con `per_page`.
- Las operaciones de escritura requieren autenticacion Sanctum.
- `DELETE` usa soft delete cuando el modelo lo soporta.
- Los valores cargados manualmente se crean con `modalidad_carga_id` y `usuario_carga_id`.
- La validacion de datos se modela sobre `estado_dato_id`, `usuario_valida_id` y `validado_at`.

## 1. Datos Fuente

Representa la definicion estable del dato operativo que luego puede ser utilizado por indicadores o corridas.

### Rutas

```http
GET    /api/datos-fuente
POST   /api/datos-fuente
GET    /api/datos-fuente/{datoFuente}
PUT    /api/datos-fuente/{datoFuente}
DELETE /api/datos-fuente/{datoFuente}
```

### Request `POST/PUT`

```json
{
  "codigo_interno": "VACUNADOS_MENSUAL",
  "area_municipal_id": 3,
  "unidad_medida_id": 2,
  "periodicidad_id": 1,
  "modalidad_carga_id": 1,
  "fuente_institucional_id": 4,
  "responsable_usuario_id": 1,
  "nombre": "Cantidad mensual de vacunados",
  "descripcion": "Total de personas vacunadas por mes",
  "tipo_dato": "decimal",
  "metodo_obtencion": "Consolidacion desde sistema nominal",
  "link_fuente": "https://ejemplo.gob.ar/fuente",
  "rango_minimo": 0,
  "rango_maximo": 999999,
  "nivel_geografico": "MUNICIPAL",
  "activo": true
}
```

### Response `show`

```json
{
  "id": 9,
  "codigo_interno": "VACUNADOS_MENSUAL",
  "area_municipal_id": 3,
  "unidad_medida_id": 2,
  "periodicidad_id": 1,
  "modalidad_carga_id": 1,
  "fuente_institucional_id": 4,
  "responsable_usuario_id": 1,
  "nombre": "Cantidad mensual de vacunados",
  "descripcion": "Total de personas vacunadas por mes",
  "tipo_dato": "decimal",
  "metodo_obtencion": "Consolidacion desde sistema nominal",
  "link_fuente": "https://ejemplo.gob.ar/fuente",
  "rango_minimo": "0.000000",
  "rango_maximo": "999999.000000",
  "nivel_geografico": "MUNICIPAL",
  "activo": true,
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

### Request `POST /importar`

Cuando la API externa no devuelve un periodo o una jurisdiccion resoluble, el endpoint admite fallbacks por request:

```json
{
  "jurisdiccion_id": 3,
  "periodo_referencia": "2026-03-01",
  "fecha_produccion": "2026-03-31",
  "estado_dato_id": 2,
  "vigente": true
}
```

## 2. Valores De Datos Fuente

Representa cada observacion cargada o importada para un dato fuente, una jurisdiccion y un periodo.

### Rutas

```http
GET    /api/datos-fuente/{datoFuente}/valores
POST   /api/datos-fuente/{datoFuente}/valores
GET    /api/datos-fuente/{datoFuente}/valores/{valor}
PUT    /api/datos-fuente/{datoFuente}/valores/{valor}
POST   /api/datos-fuente/{datoFuente}/valores/{valor}/validar
DELETE /api/datos-fuente/{datoFuente}/valores/{valor}
```

### Request `POST/PUT`

```json
{
  "jurisdiccion_id": 1,
  "estado_dato_id": 1,
  "modalidad_carga_id": 1,
  "valor_crudo": 15234.75,
  "valor_utilizado": 15234.75,
  "periodo_referencia": "2026-03-01",
  "fecha_produccion": "2026-03-31",
  "observado_motivo": null,
  "vigente": true
}
```

### Request `POST /validar`

```json
{
  "estado_dato_id": 2,
  "valor_utilizado": 15234.75,
  "observado_motivo": null
}
```

### Response `show`

```json
{
  "id": 55,
  "dato_fuente_id": 9,
  "jurisdiccion_id": 1,
  "estado_dato_id": 2,
  "modalidad_carga_id": 1,
  "usuario_carga_id": 1,
  "usuario_valida_id": 2,
  "valor_crudo": "15234.750000",
  "valor_utilizado": "15234.750000",
  "periodo_referencia": "2026-03-01",
  "fecha_produccion": "2026-03-31",
  "fecha_carga": "2026-04-29T12:00:00.000000Z",
  "validado_at": "2026-04-29T13:00:00.000000Z",
  "observado_motivo": null,
  "vigente": true,
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T13:00:00.000000Z",
  "evidencias": []
}
```

## 3. Evidencias De Dato

Representa archivos o referencias externas que respaldan un valor cargado.

### Rutas

```http
GET    /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias
POST   /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias
GET    /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias/{evidencia}
PUT    /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias/{evidencia}
DELETE /api/datos-fuente/{datoFuente}/valores/{valor}/evidencias/{evidencia}
```

### Request `POST/PUT`

```json
{
  "nombre_archivo": "vacunacion_marzo_2026.pdf",
  "url": "https://storage.ejemplo.local/evidencias/vacunacion_marzo_2026.pdf",
  "hash_archivo": "sha256:abc123",
  "descripcion": "Reporte consolidado firmado por el area"
}
```

### Response `show`

```json
{
  "id": 101,
  "dato_fuente_valor_id": 55,
  "nombre_archivo": "vacunacion_marzo_2026.pdf",
  "url": "https://storage.ejemplo.local/evidencias/vacunacion_marzo_2026.pdf",
  "hash_archivo": "sha256:abc123",
  "descripcion": "Reporte consolidado firmado por el area",
  "usuario_id": 1,
  "created_at": "2026-04-29T12:10:00.000000Z",
  "updated_at": "2026-04-29T12:10:00.000000Z"
}
```

## 4. Configuracion API Por Dato Fuente

Representa la configuracion de una fuente HTTP desde la que se puede importar un dato.

### Rutas

```http
GET    /api/datos-fuente/{datoFuente}/api-configs
POST   /api/datos-fuente/{datoFuente}/api-configs
GET    /api/datos-fuente/{datoFuente}/api-configs/{config}
PUT    /api/datos-fuente/{datoFuente}/api-configs/{config}
DELETE /api/datos-fuente/{datoFuente}/api-configs/{config}
POST   /api/datos-fuente/{datoFuente}/api-configs/{config}/probar
POST   /api/datos-fuente/{datoFuente}/api-configs/{config}/importar
GET    /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones
```

### Request `POST/PUT`

```json
{
  "nombre": "API Vacunacion Provincial",
  "metodo_http": "GET",
  "url": "https://api.ejemplo.gob.ar/v1/vacunacion",
  "auth_tipo": "BEARER",
  "headers_json": {
    "Authorization": "Bearer {{TOKEN}}"
  },
  "params_json": {
    "jurisdiccion": "LUJAN"
  },
  "json_path_valor": "data.total",
  "json_path_periodo": "data.periodo",
  "json_path_jurisdiccion": "data.jurisdiccion",
  "unidad_esperada": "personas",
  "activo": true
}
```

### Response `show`

```json
{
  "id": 4,
  "dato_fuente_id": 9,
  "nombre": "API Vacunacion Provincial",
  "metodo_http": "GET",
  "url": "https://api.ejemplo.gob.ar/v1/vacunacion",
  "auth_tipo": "BEARER",
  "headers_json": {
    "Authorization": "Bearer {{TOKEN}}"
  },
  "params_json": {
    "jurisdiccion": "LUJAN"
  },
  "json_path_valor": "data.total",
  "json_path_periodo": "data.periodo",
  "json_path_jurisdiccion": "data.jurisdiccion",
  "unidad_esperada": "personas",
  "activo": true,
  "paths": [],
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

## 5. Paths Alternativos De Extraccion

Representa alternativas ordenadas de `json_path_valor` para manejar cambios menores del proveedor externo.

### Rutas

```http
GET    /api/datos-fuente/{datoFuente}/api-configs/{config}/paths
POST   /api/datos-fuente/{datoFuente}/api-configs/{config}/paths
PUT    /api/datos-fuente/{datoFuente}/api-configs/{config}/paths/{path}
DELETE /api/datos-fuente/{datoFuente}/api-configs/{config}/paths/{path}
```

### Request `POST/PUT`

```json
{
  "json_path_valor": "result.valor",
  "prioridad": 2,
  "activo": true
}
```

## 6. Historial De Importaciones API

Representa la auditoria operativa de cada intento de consulta/importacion externa.

### Rutas

```http
GET /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones
GET /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones/{importacion}
```

### Response `show`

```json
{
  "id": 88,
  "dato_fuente_api_config_id": 4,
  "fecha_importacion": "2026-04-29T12:00:00.000000Z",
  "estado": "OK",
  "http_status": 200,
  "json_path_usado": "data.total",
  "valor_extraido": "15234.750000",
  "mensaje_error": null,
  "muestra_respuesta": {
    "data": {
      "total": 15234.75
    }
  },
  "created_at": "2026-04-29T12:00:00.000000Z",
  "updated_at": "2026-04-29T12:00:00.000000Z"
}
```

## Filtros Propuestos

### Datos Fuente

```http
GET /api/datos-fuente?per_page=15&activo=true&modalidad_carga_id=1
```

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

```http
GET /api/datos-fuente/{datoFuente}/valores?jurisdiccion_id=1&periodo_referencia=2026-03-01&vigente=true
```

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

```http
GET /api/datos-fuente/{datoFuente}/api-configs?activo=true
```

Filtros:

- `per_page`
- `activo`
- `metodo_http`
- `auth_tipo`

### Importaciones API

```http
GET /api/datos-fuente/{datoFuente}/api-configs/{config}/importaciones?estado=ERROR
```

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
4. La validacion debe registrar `usuario_valida_id` y `validado_at`.
5. Un valor observado debe poder persistir `observado_motivo`.
6. Las evidencias siempre cuelgan de un valor ya existente.
7. Una configuracion API siempre pertenece a un solo `dato_fuente`.
8. Los paths alternativos no reemplazan la configuracion principal; la complementan con prioridad.
9. Cada intento de importacion debe quedar auditado en `datos_fuente_api_importaciones`, incluso si falla.
10. La importacion automatica no reemplaza una validacion humana; solo genera o actualiza valores segun la estrategia operativa que se apruebe en implementacion.

## Decisiones Operativas Para La Implementacion

1. El modulo se expone bajo `/api/datos-fuente`.
2. La validacion de valores se modela como accion explicita `POST /validar`.
3. Las evidencias se gestionan como recurso hijo del valor.
4. Las configuraciones API y sus paths se gestionan como recursos hijos del `dato_fuente`.
5. El endpoint `/probar` no persiste valores productivos; solo registra el intento tecnico en importaciones cuando se considere necesario.
6. El endpoint `/importar` puede crear o actualizar valores, pero debe dejar trazabilidad completa del intento.
7. Si la API no provee jurisdiccion o periodo con un path usable, la implementacion puede resolverlos con fallbacks enviados por request.
