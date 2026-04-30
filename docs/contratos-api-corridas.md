# Contratos API \- Corridas Y Snapshots

Este documento define el contrato propuesto para el circuito inicial de corridas del backend SICIS. Cubre la creación de corridas, su ejecución, aprobación, publicación y la persistencia de snapshots de datos e indicadores.

## Alcance

Este bloque cubre:

1. `corridas`  
2. `corridas_snapshot_datos`  
3. `corridas_snapshot_indicadores`

Quedan fuera de este documento:

1. motor completo y genérico de fórmulas  
2. reglas avanzadas de observación manual de resultados  
3. publicación externa o portal visual

## Permisos Propuestos

- Lectura de corridas y resultados: `indicadores.ver`  
- Ejecución y edición operativa: `corridas.ejecutar`  
- Aprobación: `corridas.aprobar`  
- Publicación: `resultados.publicar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/corridas`.  
- Todas las respuestas son JSON.  
- Los listados son paginados con `per_page`.  
- Todas las operaciones requieren autenticación Sanctum.  
- La corrida se ejecuta por `jurisdiccion` y `periodo_referencia`.  
- La ejecución usa solo datos fuente `VALIDADO` y `vigente`.  
- La primera implementación prioriza fórmulas simples y trazabilidad sobre cobertura total de expresiones.

## 1. Corridas

### Rutas

GET    /api/corridas

POST   /api/corridas

GET    /api/corridas/{corrida}

PUT    /api/corridas/{corrida}

POST   /api/corridas/{corrida}/ejecutar

POST   /api/corridas/{corrida}/aprobar

POST   /api/corridas/{corrida}/publicar

### Request `POST/PUT`

{

  "jurisdiccion\_id": 3,

  "titulo": "Corrida Luján marzo 2026",

  "periodo\_referencia": "2026-03-01",

  "observaciones": "Primera corrida de prueba"

}

### Response `show`

{

  "id": 4,

  "jurisdiccion\_id": 3,

  "estado\_corrida\_id": 2,

  "titulo": "Corrida Luján marzo 2026",

  "periodo\_referencia": "2026-03-01",

  "usuario\_ejecucion\_id": 1,

  "usuario\_aprobacion\_id": null,

  "ejecutada\_at": "2026-04-30T12:00:00.000000Z",

  "aprobada\_at": null,

  "publicada\_at": null,

  "observaciones": "Primera corrida de prueba",

  "snapshot\_datos": [],

  "snapshot\_indicadores": []

}

## 2. Acción De Ejecución

### Request `POST /ejecutar`

{

  "observaciones": "Reejecución luego de validar faltantes"

}

### Comportamiento Esperado

1. Seleccionar indicadores activos.  
2. Resolver la versión metodológica activa para el periodo.  
3. Buscar datos fuente validados para la jurisdicción y el periodo.  
4. Persistir `corridas_snapshot_datos` con los datos efectivamente usados.  
5. Persistir `corridas_snapshot_indicadores` con el resultado de cada indicador.  
6. Dejar la corrida en estado `EJECUTADA` si el proceso cierra, aunque algunos indicadores queden `SIN_DATOS` o `ERROR_CALCULO`.

## 3. Acción De Aprobación

### Request `POST /aprobar`

{

  "observaciones": "Revision metodológica aprobada"

}

## 4. Acción De Publicación

### Request `POST /publicar`

{

  "observaciones": "Habilitar para consumo externo"

}

## Filtros Propuestos

### Corridas

GET /api/corridas?jurisdiccion\_id=3\&estado\_corrida\_id=2\&periodo\_referencia=2026-03-01

Filtros:

- `per_page`  
- `jurisdiccion_id`  
- `estado_corrida_id`  
- `periodo_referencia`  
- `usuario_ejecucion_id`  
- `usuario_aprobacion_id`

## Reglas De Negocio A Respetar

1. La corrida debe pertenecer a una `jurisdiccion` existente.  
2. La ejecución debe usar datos fuente `VALIDADO` y `vigente`.  
3. Cada snapshot de dato debe quedar asociado a la corrida, el dato fuente y el valor puntual usado.  
4. Cada snapshot de indicador debe quedar asociado a la corrida, el indicador y la versión metodológica usada.  
5. La aprobación solo debería ocurrir sobre corridas `EJECUTADA`.  
6. La publicación solo debería ocurrir sobre corridas `APROBADA`.  
7. La primera implementación puede resolver solo fórmulas simples, pero debe dejar `ERROR_CALCULO` cuando no pueda calcular.

## Decisiones Operativas Para La Implementación

1. El módulo se expone bajo `/api/corridas`.  
2. La lectura reutiliza `indicadores.ver`.  
3. La ejecución se protege con `corridas.ejecutar`.  
4. La aprobación se protege con `corridas.aprobar`.  
5. La publicación se protege con `resultados.publicar`.  
6. La primera implementación prioriza `RATIO_CONSTANTE` y casos simples de una variable.
