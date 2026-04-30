# Convenciones API - Errores Y Observabilidad

Este documento resume las convenciones tecnicas de errores y trazabilidad adoptadas en el backend SICIS para mejorar operacion, diagnostico y consistencia entre modulos.

## Objetivo

Unificar la forma en que la API responde ante errores comunes y dejar una referencia simple para desarrollo, QA e integraciones.

## Cabecera Operativa

Todas las respuestas del backend incluyen:

- `X-Request-Id`

Ese valor sirve para correlacionar un error devuelto al cliente con el registro correspondiente en logs.

## Forma Base De Error

Las respuestas de error siguen esta estructura:

```json
{
  "ok": false,
  "message": "Mensaje operativo",
  "code": "CODIGO_ESTABLE",
  "errors": {
    "campo": [
      "Detalle de validacion"
    ]
  },
  "request_id": "uuid",
  "path": "api/ruta"
}
```

Notas:

1. `errors` solo aparece cuando aplica, principalmente en `422`.
2. `request_id` siempre debe preservarse para soporte tecnico.
3. `path` ayuda a diagnostico rapido en logs y QA.

## Codigos Y Estados

### 401

- `code`: `AUTH_REQUIRED`
- mensaje: autenticacion faltante o invalida

### 403

- `code`: `FORBIDDEN`
- mensaje: permiso insuficiente

### 404

- `code`: `NOT_FOUND`
- mensaje: recurso inexistente

### 422

- `code`: `VALIDATION_ERROR`
- mensaje: validacion fallida o restriccion de negocio expuesta como validacion

### 500

- `code`: `INTERNAL_ERROR`
- mensaje: error interno no esperado

## Criterios Practicos

1. Las validaciones de negocio recuperables deben priorizar `422`.
2. Los problemas de permiso deben priorizar `403`.
3. Los errores no esperados no deben devolver trazas ni detalles sensibles al cliente.
4. Los detalles tecnicos completos deben quedar en logs y asociarse por `request_id`.

## Logging Transversal

Se registra log estructurado para excepciones no esperadas con:

- `request_id`
- `method`
- `path`
- `usuario_id`
- clase de excepcion
- mensaje

## Observaciones Por Modulo

1. Corridas mantiene auditoria y alertas de negocio propias.
2. Datos fuente mantiene trazabilidad especifica de importaciones API.
3. La capa global de errores no reemplaza la auditoria funcional; la complementa.
