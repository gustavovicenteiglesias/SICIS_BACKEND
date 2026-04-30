# Contratos API - Corridas Y Snapshots

Este documento define el contrato propuesto para el circuito inicial de corridas del backend SICIS. Cubre la creacion de corridas, su ejecucion, aprobacion, publicacion y la persistencia de snapshots de datos e indicadores.

## Alcance

Este bloque cubre:

1. `corridas`
2. `corridas_snapshot_datos`
3. `corridas_snapshot_indicadores`

Quedan fuera de este documento:

1. motor completo y generico de formulas
2. reglas avanzadas de observacion manual de resultados
3. publicacion externa o portal visual

## Permisos Propuestos

- Lectura de corridas y resultados: `indicadores.ver`
- Ejecucion y edicion operativa: `corridas.ejecutar`
- Aprobacion: `corridas.aprobar`
- Publicacion: `resultados.publicar`

## Convenciones Generales

- Todas las rutas viven bajo `/api/corridas`.
- Todas las respuestas son JSON.
- Los listados son paginados con `per_page`.
- Todas las operaciones requieren autenticacion Sanctum.
- La corrida se ejecuta por `jurisdiccion` y `periodo_referencia`.
- La ejecucion usa solo datos fuente `VALIDADO` y `vigente`.
- La primera implementacion prioriza formulas simples y trazabilidad sobre cobertura total de expresiones.

## 1. Corridas

### Rutas

```http
GET    /api/corridas
POST   /api/corridas
GET    /api/corridas/{corrida}
PUT    /api/corridas/{corrida}
POST   /api/corridas/{corrida}/ejecutar
POST   /api/corridas/{corrida}/aprobar
POST   /api/corridas/{corrida}/publicar
```

### Request `POST/PUT`

```json
{
  "jurisdiccion_id": 3,
  "titulo": "Corrida Lujan marzo 2026",
  "periodo_referencia": "2026-03-01",
  "observaciones": "Primera corrida de prueba"
}
```

### Response `show`

```json
{
  "id": 4,
  "jurisdiccion_id": 3,
  "estado_corrida_id": 2,
  "titulo": "Corrida Lujan marzo 2026",
  "periodo_referencia": "2026-03-01",
  "usuario_ejecucion_id": 1,
  "usuario_aprobacion_id": null,
  "ejecutada_at": "2026-04-30T12:00:00.000000Z",
  "aprobada_at": null,
  "publicada_at": null,
  "observaciones": "Primera corrida de prueba",
  "snapshot_datos": [],
  "snapshot_indicadores": []
}
```

## 2. Accion De Ejecucion

### Request `POST /ejecutar`

```json
{
  "observaciones": "Reejecucion luego de validar faltantes"
}
```

### Comportamiento Esperado

1. Seleccionar indicadores activos.
2. Resolver la version metodologica activa para el periodo.
3. Buscar datos fuente validados para la jurisdiccion y el periodo.
4. Persistir `corridas_snapshot_datos` con los datos efectivamente usados.
5. Persistir `corridas_snapshot_indicadores` con el resultado de cada indicador.
6. Dejar la corrida en estado `EJECUTADA` si el proceso cierra, aunque algunos indicadores queden `SIN_DATOS` o `ERROR_CALCULO`.

## 3. Accion De Aprobacion

### Request `POST /aprobar`

```json
{
  "observaciones": "Revision metodologica aprobada"
}
```

## 4. Accion De Publicacion

### Request `POST /publicar`

```json
{
  "observaciones": "Habilitar para consumo externo"
}
```

## Filtros Propuestos

### Corridas

```http
GET /api/corridas?jurisdiccion_id=3&estado_corrida_id=2&periodo_referencia=2026-03-01
```

Filtros:

- `per_page`
- `jurisdiccion_id`
- `estado_corrida_id`
- `periodo_referencia`
- `usuario_ejecucion_id`
- `usuario_aprobacion_id`

## Reglas De Negocio A Respetar

1. La corrida debe pertenecer a una `jurisdiccion` existente.
2. La ejecucion debe usar datos fuente `VALIDADO` y `vigente`.
3. Cada snapshot de dato debe quedar asociado a la corrida, el dato fuente y el valor puntual usado.
4. Cada snapshot de indicador debe quedar asociado a la corrida, el indicador y la version metodologica usada.
5. La aprobacion solo deberia ocurrir sobre corridas `EJECUTADA`.
6. La publicacion solo deberia ocurrir sobre corridas `APROBADA`.
7. La primera implementacion puede resolver solo formulas simples, pero debe dejar `ERROR_CALCULO` cuando no pueda calcular.

## Decisiones Operativas Para La Implementacion

1. El modulo se expone bajo `/api/corridas`.
2. La lectura reutiliza `indicadores.ver`.
3. La ejecucion se protege con `corridas.ejecutar`.
4. La aprobacion se protege con `corridas.aprobar`.
5. La publicacion se protege con `resultados.publicar`.
6. La primera implementacion prioriza `RATIO_CONSTANTE` y casos simples de una variable.
